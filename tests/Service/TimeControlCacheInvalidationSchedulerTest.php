<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Service;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Defaults;
use Blur\BlurElysiumSlider\Message\TimeControlCacheInvalidationMessage;
use Blur\BlurElysiumSlider\Service\TimeControlCacheInvalidationScheduler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class TimeControlCacheInvalidationSchedulerTest extends TestCase
{
    private SpyMessageBus $messageBus;

    private MockClock $clock;

    private TimeControlCacheInvalidationScheduler $scheduler;

    protected function setUp(): void
    {
        $this->messageBus = new SpyMessageBus();
        $this->clock = new MockClock('2025-06-15 12:00:00');
        $this->scheduler = new TimeControlCacheInvalidationScheduler($this->messageBus, $this->clock);
    }

    // =====================================================
    // entityConfig() Tests
    // =====================================================

    public function testEntityConfigContainsElysiumSlides(): void
    {
        $config = TimeControlCacheInvalidationScheduler::entityConfig();

        static::assertArrayHasKey(ElysiumSlidesDefinition::ENTITY_NAME, $config);
        static::assertSame('id', $config[ElysiumSlidesDefinition::ENTITY_NAME]['id_field']);
        static::assertSame('activeFrom', $config[ElysiumSlidesDefinition::ENTITY_NAME]['active_from']);
        static::assertSame('activeUntil', $config[ElysiumSlidesDefinition::ENTITY_NAME]['active_until']);
    }

    public function testEntityConfigContainsCmsSection(): void
    {
        $config = TimeControlCacheInvalidationScheduler::entityConfig();

        static::assertArrayHasKey(CmsSectionDefinition::ENTITY_NAME, $config);
        static::assertSame('pageId', $config[CmsSectionDefinition::ENTITY_NAME]['id_field']);
        static::assertSame(
            'customFields.' . Defaults::CMS_SECTION_SETTINGS_KEY . '.activeFrom',
            $config[CmsSectionDefinition::ENTITY_NAME]['active_from']
        );
        static::assertSame(
            'customFields.' . Defaults::CMS_SECTION_SETTINGS_KEY . '.activeUntil',
            $config[CmsSectionDefinition::ENTITY_NAME]['active_until']
        );
    }

    // =====================================================
    // Null / Invalid ID Tests
    // =====================================================

    public function testScheduleReturnsEarlyWhenIdIsNull(): void
    {
        $writeResult = $this->createWriteResult(null, ['activeFrom' => '2025-06-16 00:00:00']);

        $this->scheduler->schedule($writeResult, ElysiumSlidesDefinition::ENTITY_NAME);

        static::assertCount(0, $this->messageBus->getDispatched());
    }

    public function testScheduleReturnsEarlyWhenIdIsInvalidUuid(): void
    {
        $writeResult = $this->createWriteResult('not-a-valid-uuid', ['activeFrom' => '2025-06-16 00:00:00']);

        $this->scheduler->schedule($writeResult, ElysiumSlidesDefinition::ENTITY_NAME);

        static::assertCount(0, $this->messageBus->getDispatched());
    }

    // =====================================================
    // Future Time Tests (Delayed Dispatch)
    // =====================================================

    public function testScheduleDispatchesDelayedMessageForFutureActiveFrom(): void
    {
        $id = Uuid::randomHex();
        $futureTime = '2025-06-16 12:00:00';
        $writeResult = $this->createWriteResult($id, ['activeFrom' => $futureTime, 'activeUntil' => null]);

        $this->scheduler->schedule($writeResult, ElysiumSlidesDefinition::ENTITY_NAME);

        $dispatched = $this->messageBus->getDispatched();
        static::assertCount(1, $dispatched);

        $envelope = $dispatched[0];
        static::assertInstanceOf(Envelope::class, $envelope);

        $message = $envelope->getMessage();
        static::assertInstanceOf(TimeControlCacheInvalidationMessage::class, $message);
        static::assertSame($id, $message->getEntityId());
        static::assertSame(ElysiumSlidesDefinition::ENTITY_NAME, $message->getEntityName());
        static::assertEquals(new \DateTimeImmutable($futureTime), $message->getInvalidationTime());

        $delayStamps = $envelope->all(DelayStamp::class);
        static::assertCount(1, $delayStamps);
        static::assertSame(86400 * 1000, $delayStamps[0]->getDelay());
    }

    public function testScheduleDispatchesDelayedMessageForFutureActiveUntil(): void
    {
        $id = Uuid::randomHex();
        $futureTime = '2025-06-17 12:00:00';
        $writeResult = $this->createWriteResult($id, ['activeFrom' => null, 'activeUntil' => $futureTime]);

        $this->scheduler->schedule($writeResult, ElysiumSlidesDefinition::ENTITY_NAME);

        $dispatched = $this->messageBus->getDispatched();
        static::assertCount(1, $dispatched);

        $envelope = $dispatched[0];
        static::assertInstanceOf(Envelope::class, $envelope);

        $delayStamps = $envelope->all(DelayStamp::class);
        static::assertCount(1, $delayStamps);
        static::assertSame(86400 * 2 * 1000, $delayStamps[0]->getDelay());
    }

    public function testScheduleDispatchesTwoDelayedMessagesWhenBothTimesAreFuture(): void
    {
        $id = Uuid::randomHex();
        $writeResult = $this->createWriteResult($id, [
            'activeFrom' => '2025-06-16 12:00:00',
            'activeUntil' => '2025-06-17 12:00:00',
        ]);

        $this->scheduler->schedule($writeResult, ElysiumSlidesDefinition::ENTITY_NAME);

        $dispatched = $this->messageBus->getDispatched();
        static::assertCount(2, $dispatched);

        foreach ($dispatched as $envelope) {
            static::assertInstanceOf(Envelope::class, $envelope);
            static::assertCount(1, $envelope->all(DelayStamp::class));
        }
    }

    // =====================================================
    // Past Time Tests (Immediate vs No Dispatch)
    // =====================================================

    public function testScheduleDispatchesImmediatelyForPastActiveFromOnSlides(): void
    {
        $id = Uuid::randomHex();
        $pastTime = '2025-06-14 12:00:00';
        $writeResult = $this->createWriteResult($id, ['activeFrom' => $pastTime, 'activeUntil' => null]);

        $this->scheduler->schedule($writeResult, ElysiumSlidesDefinition::ENTITY_NAME);

        $dispatched = $this->messageBus->getDispatched();
        static::assertCount(1, $dispatched);

        $message = $dispatched[0];
        static::assertInstanceOf(TimeControlCacheInvalidationMessage::class, $message);
        static::assertSame($id, $message->getEntityId());
        static::assertSame(ElysiumSlidesDefinition::ENTITY_NAME, $message->getEntityName());
        static::assertEquals(new \DateTimeImmutable($pastTime), $message->getInvalidationTime());
    }

    public function testScheduleDispatchesImmediatelyForPastActiveUntilOnSlides(): void
    {
        $id = Uuid::randomHex();
        $pastTime = '2025-06-14 12:00:00';
        $writeResult = $this->createWriteResult($id, ['activeFrom' => null, 'activeUntil' => $pastTime]);

        $this->scheduler->schedule($writeResult, ElysiumSlidesDefinition::ENTITY_NAME);

        $dispatched = $this->messageBus->getDispatched();
        static::assertCount(1, $dispatched);
        static::assertInstanceOf(TimeControlCacheInvalidationMessage::class, $dispatched[0]);
    }

    public function testScheduleDoesNotDispatchForPastActiveFromOnCmsSection(): void
    {
        $id = Uuid::randomHex();
        $pastTime = '2025-06-14 12:00:00';
        $writeResult = $this->createWriteResult($id, [
            'customFields' => [
                Defaults::CMS_SECTION_SETTINGS_KEY => [
                    'activeFrom' => $pastTime,
                    'activeUntil' => null,
                ],
            ],
        ]);

        $this->scheduler->schedule($writeResult, CmsSectionDefinition::ENTITY_NAME);

        static::assertCount(0, $this->messageBus->getDispatched());
    }

    public function testScheduleDoesNotDispatchForPastActiveUntilOnCmsSection(): void
    {
        $id = Uuid::randomHex();
        $pastTime = '2025-06-14 12:00:00';
        $writeResult = $this->createWriteResult($id, [
            'customFields' => [
                Defaults::CMS_SECTION_SETTINGS_KEY => [
                    'activeFrom' => null,
                    'activeUntil' => $pastTime,
                ],
            ],
        ]);

        $this->scheduler->schedule($writeResult, CmsSectionDefinition::ENTITY_NAME);

        static::assertCount(0, $this->messageBus->getDispatched());
    }

    // =====================================================
    // Null Value Tests
    // =====================================================

    public function testScheduleSkipsNullActiveFrom(): void
    {
        $id = Uuid::randomHex();
        $futureTime = '2025-06-16 12:00:00';
        $writeResult = $this->createWriteResult($id, ['activeFrom' => null, 'activeUntil' => $futureTime]);

        $this->scheduler->schedule($writeResult, ElysiumSlidesDefinition::ENTITY_NAME);

        $dispatched = $this->messageBus->getDispatched();
        static::assertCount(1, $dispatched);

        $message = $dispatched[0] instanceof Envelope ? $dispatched[0]->getMessage() : $dispatched[0];
        static::assertInstanceOf(TimeControlCacheInvalidationMessage::class, $message);
        static::assertEquals(new \DateTimeImmutable($futureTime), $message->getInvalidationTime());
    }

    public function testScheduleSkipsNullActiveUntil(): void
    {
        $id = Uuid::randomHex();
        $futureTime = '2025-06-16 12:00:00';
        $writeResult = $this->createWriteResult($id, ['activeFrom' => $futureTime, 'activeUntil' => null]);

        $this->scheduler->schedule($writeResult, ElysiumSlidesDefinition::ENTITY_NAME);

        $dispatched = $this->messageBus->getDispatched();
        static::assertCount(1, $dispatched);

        $envelope = $dispatched[0];
        static::assertInstanceOf(Envelope::class, $envelope);
        static::assertCount(1, $envelope->all(DelayStamp::class));
    }

    public function testScheduleDoesNothingWhenBothTimesAreNull(): void
    {
        $id = Uuid::randomHex();
        $writeResult = $this->createWriteResult($id, ['activeFrom' => null, 'activeUntil' => null]);

        $this->scheduler->schedule($writeResult, ElysiumSlidesDefinition::ENTITY_NAME);

        static::assertCount(0, $this->messageBus->getDispatched());
    }

    // =====================================================
    // DateTimeInterface Handling Tests
    // =====================================================

    public function testScheduleHandlesDateTimeInterfaceForActiveFrom(): void
    {
        $id = Uuid::randomHex();
        $futureDateTime = new \DateTimeImmutable('2025-06-16 12:00:00');
        $writeResult = $this->createWriteResult($id, ['activeFrom' => $futureDateTime, 'activeUntil' => null]);

        $this->scheduler->schedule($writeResult, ElysiumSlidesDefinition::ENTITY_NAME);

        $dispatched = $this->messageBus->getDispatched();
        static::assertCount(1, $dispatched);

        $envelope = $dispatched[0];
        static::assertInstanceOf(Envelope::class, $envelope);

        $delayStamps = $envelope->all(DelayStamp::class);
        static::assertCount(1, $delayStamps);
        static::assertSame(86400 * 1000, $delayStamps[0]->getDelay());
    }

    public function testScheduleHandlesDateTimeInterfaceForActiveUntil(): void
    {
        $id = Uuid::randomHex();
        $pastDateTime = new \DateTimeImmutable('2025-06-14 12:00:00');
        $writeResult = $this->createWriteResult($id, ['activeFrom' => null, 'activeUntil' => $pastDateTime]);

        $this->scheduler->schedule($writeResult, ElysiumSlidesDefinition::ENTITY_NAME);

        $dispatched = $this->messageBus->getDispatched();
        static::assertCount(1, $dispatched);
        static::assertInstanceOf(TimeControlCacheInvalidationMessage::class, $dispatched[0]);
    }

    // =====================================================
    // CMS Section Nested Path Tests
    // =====================================================

    public function testScheduleDispatchesDelayedMessageForCmsSectionWithFutureActiveFrom(): void
    {
        $id = Uuid::randomHex();
        $futureTime = '2025-06-16 12:00:00';
        $writeResult = $this->createWriteResult($id, [
            'customFields' => [
                Defaults::CMS_SECTION_SETTINGS_KEY => [
                    'activeFrom' => $futureTime,
                    'activeUntil' => null,
                ],
            ],
        ]);

        $this->scheduler->schedule($writeResult, CmsSectionDefinition::ENTITY_NAME);

        $dispatched = $this->messageBus->getDispatched();
        static::assertCount(1, $dispatched);

        $envelope = $dispatched[0];
        static::assertInstanceOf(Envelope::class, $envelope);

        $message = $envelope->getMessage();
        static::assertInstanceOf(TimeControlCacheInvalidationMessage::class, $message);
        static::assertSame($id, $message->getEntityId());
        static::assertSame(CmsSectionDefinition::ENTITY_NAME, $message->getEntityName());

        $delayStamps = $envelope->all(DelayStamp::class);
        static::assertCount(1, $delayStamps);
        static::assertSame(86400 * 1000, $delayStamps[0]->getDelay());
    }

    public function testScheduleHandlesCmsSectionWithMissingNestedCustomFields(): void
    {
        $id = Uuid::randomHex();
        $writeResult = $this->createWriteResult($id, ['customFields' => []]);

        $this->scheduler->schedule($writeResult, CmsSectionDefinition::ENTITY_NAME);

        static::assertCount(0, $this->messageBus->getDispatched());
    }

    // =====================================================
    // Exact Timestamp Delay Tests
    // =====================================================

    #[DataProvider('delayCalculationProvider')]
    public function testScheduleCalculatesCorrectDelaySeconds(string $futureTime, int $expectedDelayMs): void
    {
        $id = Uuid::randomHex();
        $writeResult = $this->createWriteResult($id, ['activeFrom' => $futureTime, 'activeUntil' => null]);

        $this->scheduler->schedule($writeResult, ElysiumSlidesDefinition::ENTITY_NAME);

        $dispatched = $this->messageBus->getDispatched();
        static::assertCount(1, $dispatched);

        $envelope = $dispatched[0];
        static::assertInstanceOf(Envelope::class, $envelope);

        $delayStamps = $envelope->all(DelayStamp::class);
        static::assertCount(1, $delayStamps);
        static::assertSame($expectedDelayMs, $delayStamps[0]->getDelay());
    }

    public static function delayCalculationProvider(): array
    {
        return [
            '1 hour in future' => ['2025-06-15 13:00:00', 3600 * 1000],
            '1 minute in future' => ['2025-06-15 12:01:00', 60 * 1000],
            '1 second in future' => ['2025-06-15 12:00:01', 1 * 1000],
            '24 hours in future' => ['2025-06-16 12:00:00', 86400 * 1000],
        ];
    }

    // =====================================================
    // Edge Case: Exact Now Timestamp
    // =====================================================

    public function testScheduleDispatchesImmediatelyWhenTimeIsExactlyNow(): void
    {
        $id = Uuid::randomHex();
        $writeResult = $this->createWriteResult($id, ['activeFrom' => '2025-06-15 12:00:00', 'activeUntil' => null]);

        $this->scheduler->schedule($writeResult, ElysiumSlidesDefinition::ENTITY_NAME);

        $dispatched = $this->messageBus->getDispatched();
        static::assertCount(1, $dispatched);
        static::assertInstanceOf(TimeControlCacheInvalidationMessage::class, $dispatched[0]);
    }

    // =====================================================
    // Helper Methods
    // =====================================================

    private function createWriteResult(?string $id, array $payload): EntityWriteResult
    {
        $writeResult = $this->createMock(EntityWriteResult::class);
        $writeResult->method('getProperty')->willReturnCallback(function (string $property) use ($id) {
            if ($property === 'id' || $property === 'pageId') {
                return $id;
            }

            return null;
        });
        $writeResult->method('getPayload')->willReturn($payload);

        return $writeResult;
    }
}

/**
 * @internal
 */
class SpyMessageBus implements MessageBusInterface
{
    private array $dispatched = [];

    public function dispatch(object $message, array $stamps = []): Envelope
    {
        $envelope = Envelope::wrap($message, $stamps);
        $this->dispatched[] = $stamps === [] ? $message : $envelope;

        return $envelope;
    }

    /**
     * @return list<Envelope|object>
     */
    public function getDispatched(): array
    {
        return $this->dispatched;
    }
}
