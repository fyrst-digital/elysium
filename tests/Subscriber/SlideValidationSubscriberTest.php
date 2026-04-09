<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Subscriber;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation\ElysiumSlidesTranslationDefinition;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Defaults;
use Blur\BlurElysiumSlider\Subscriber\SlideValidationSubscriber;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\Validation\WriteConstraintViolationException;
use Shopware\Core\Framework\Uuid\Uuid;


class SlideValidationSubscriberTest extends TestCase
{
    private Connection&MockObject $connection;

    private SlideValidationSubscriber $subscriber;

    protected function setUp(): void
    {
        Feature::registerFeatures([
            'elysium_preview_time_control' => ['default' => true],
        ]);

        $this->connection = $this->createMock(Connection::class);
        $this->subscriber = new SlideValidationSubscriber($this->connection);
    }

    protected function tearDown(): void
    {
        Feature::resetRegisteredFeatures();
    }

    public function testGetSubscribedEvents(): void
    {
        $events = SlideValidationSubscriber::getSubscribedEvents();

        static::assertArrayHasKey(PreWriteValidationEvent::class, $events);
        $eventListeners = $events[PreWriteValidationEvent::class];
        static::assertIsArray($eventListeners);

        $listenerMethods = array_column($eventListeners, 0);
        static::assertContains('validateSlideName', $listenerMethods);
        static::assertContains('validateTimeControl', $listenerMethods);
    }

    // =====================================================
    // Slide Name Validation Tests
    // =====================================================

    #[DataProvider('validSlideNameProvider')]
    public function testValidatesSlideNameWithValidNames(string $name): void
    {
        $command = new TranslationTestWriteCommand('create', ['name' => $name]);
        $event = $this->createEvent([$command]);

        $this->subscriber->validateSlideName($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public static function validSlideNameProvider(): array
    {
        return [
            'simple name' => ['my-slide'],
            'name with numbers' => ['slide-2024'],
            'name with spaces' => ['my slide name'],
            'name with spaces and dashes' => ['my-slide-name-2024'],
            'single character' => ['a'],
            'single number' => ['1'],
            'name with multiple spaces' => ['a b c d e'],
            'uppercase letters' => ['MySlideName'],
            'mixed case' => ['My-Slide-Name-2024'],
        ];
    }

    #[DataProvider('invalidSlideNameProvider')]
    public function testValidatesSlideNameWithInvalidNames(string $name): void
    {
        $command = new TranslationTestWriteCommand('create', ['name' => $name]);
        $event = $this->createEvent([$command]);

        $this->subscriber->validateSlideName($event);

        $exceptions = $event->getExceptions()->getExceptions();
        static::assertCount(1, $exceptions);
        static::assertInstanceOf(WriteConstraintViolationException::class, $exceptions[0]);
    }

    public static function invalidSlideNameProvider(): array
    {
        return [
            'leading whitespace' => [' my-slide'],
            'trailing whitespace' => ['my-slide '],
            'special characters' => ['my@slide'],
            'underscore' => ['my_slide'],
            'dot' => ['my.slide'],
            'parentheses' => ['my(slide)'],
            'brackets' => ['my[slide]'],
            'equals' => ['my=slide'],
            'plus' => ['my+slide'],
            'ampersand' => ['my&slide'],
            'at sign' => ['my@slide'],
            'hash' => ['my#slide'],
            'dollar' => ['my$slide'],
            'percent' => ['my%slide'],
            'caret' => ['my^slide'],
            'asterisk' => ['my*slide'],
            'exclamation' => ['my!slide'],
            'question' => ['my?slide'],
            'pipe' => ['my|slide'],
            'backslash' => ['my\slide'],
            'forward slash' => ['my/slide'],
        ];
    }

    public function testValidateSlideNameIgnoresNullName(): void
    {
        $command = new TranslationTestWriteCommand('create', []);
        $event = $this->createEvent([$command]);

        $this->subscriber->validateSlideName($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testValidateSlideNameSkipsDeleteCommand(): void
    {
        $command = new TranslationTestWriteCommand('delete', ['name' => 'invalid!name']);
        $event = $this->createEvent([$command]);

        $this->subscriber->validateSlideName($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testValidateSlideNameSkipsOtherEntities(): void
    {
        $command = new class('create', ['name' => 'invalid!name']) extends TestWriteCommand {
            public function getEntityName(): string { return 'product'; }
        };
        $event = $this->createEvent([$command]);

        $this->subscriber->validateSlideName($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testValidateSlideNameViolationContainsCorrectMetadata(): void
    {
        $command = new TranslationTestWriteCommand('create', ['name' => 'invalid@name']);
        $event = $this->createEvent([$command]);

        $this->subscriber->validateSlideName($event);

        $exceptions = $event->getExceptions()->getExceptions();
        static::assertCount(1, $exceptions);

        $exception = $exceptions[0];
        static::assertInstanceOf(WriteConstraintViolationException::class, $exception);

        $violations = $exception->getViolations();
        static::assertCount(1, $violations);

        $violation = $violations[0];
        static::assertSame('/name', $violation->getPropertyPath());
        static::assertSame(Defaults::ERROR_CODE_NAME_FORMAT, $violation->getCode());
        static::assertSame('invalid@name', $violation->getInvalidValue());
    }

    // =====================================================
    // Time Control Validation Tests
    // =====================================================

    public function testCreateDoesNotFetchFromDatabase(): void
    {
        $command = new TestWriteCommand('create', ['active_from' => '2025-01-01 00:00:00', 'active_until' => '2025-12-31 23:59:59']);
        $event = $this->createEvent([$command]);

        $this->connection
            ->expects(static::once())
            ->method('fetchAllAssociative')
            ->willReturn([]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    #[DataProvider('validDateRangeProvider')]
    public function testValidatesCreateWithValidDates(string $activeFrom, string $activeUntil): void
    {
        $command = new TestWriteCommand('create', ['active_from' => $activeFrom, 'active_until' => $activeUntil]);
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public static function validDateRangeProvider(): array
    {
        return [
            'standard range' => ['2025-01-01 00:00:00', '2025-12-31 23:59:59'],
            'one second difference' => ['2025-06-15 12:00:00', '2025-06-15 12:00:01'],
            'one day difference' => ['2025-01-01 00:00:00', '2025-01-02 00:00:00'],
        ];
    }

    public function testValidatesCreateWithOnlyActiveFrom(): void
    {
        $command = new TestWriteCommand('create', ['active_from' => '2025-01-01 00:00:00']);
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testValidatesCreateWithOnlyActiveUntil(): void
    {
        $command = new TestWriteCommand('create', ['active_until' => '2025-12-31 23:59:59']);
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testValidatesCreateWithNoTimeControlFields(): void
    {
        $command = new TestWriteCommand('create', ['name' => 'Test Slide']);
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testValidatesUpdateWithValidDates(): void
    {
        $command = new TestWriteCommand(
            'update',
            ['active_from' => '2025-01-01 00:00:00', 'active_until' => '2025-12-31 23:59:59']
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testUpdateWithBothFieldsProvidedSkipsDatabaseFetch(): void
    {
        $id = Uuid::randomHex();
        $command = new TestWriteCommand('update', ['active_from' => '2025-01-01 00:00:00', 'active_until' => '2025-12-31 23:59:59'], $id);
        $event = $this->createEvent([$command]);

        $this->connection
            ->expects(static::once())
            ->method('fetchAllAssociative')
            ->willReturn([['id' => $id, 'active_from' => '2024-01-01 00:00:00', 'active_until' => '2024-12-31 23:59:59']]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testValidatesUpdateWithOnlyActiveFromAndFetchesExistingActiveUntil(): void
    {
        $id = Uuid::randomHex();
        $command = new TestWriteCommand('update', ['active_from' => '2025-01-01 00:00:00'], $id);
        $event = $this->createEvent([$command]);

        $this->connection
            ->expects(static::once())
            ->method('fetchAllAssociative')
            ->willReturn([['id' => $id, 'active_from' => null, 'active_until' => '2025-12-31 23:59:59']]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testValidatesUpdateWithOnlyActiveUntilAndFetchesExistingActiveFrom(): void
    {
        $id = Uuid::randomHex();
        $command = new TestWriteCommand('update', ['active_until' => '2025-12-31 23:59:59'], $id);
        $event = $this->createEvent([$command]);

        $this->connection
            ->expects(static::once())
            ->method('fetchAllAssociative')
            ->willReturn([['id' => $id, 'active_from' => '2025-01-01 00:00:00', 'active_until' => null]]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testHandlesNonExistentSlideOnUpdate(): void
    {
        $id = Uuid::randomHex();
        $command = new TestWriteCommand('update', ['active_from' => '2025-01-01 00:00:00'], $id);
        $event = $this->createEvent([$command]);

        $this->connection
            ->expects(static::once())
            ->method('fetchAllAssociative')
            ->willReturn([]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testUpdateWithoutIdDoesNotFetchDatabase(): void
    {
        $command = new class('update', ['active_from' => '2025-01-01 00:00:00']) extends TestWriteCommand {
            public function getPrimaryKey(): array { return []; }
        };
        $event = $this->createEvent([$command]);

        $this->connection
            ->expects(static::never())
            ->method('fetchAllAssociative');

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    #[DataProvider('invalidDateRangeProvider')]
    public function testThrowsViolationOnInvalidDateRange(string $activeFrom, string $activeUntil): void
    {
        $command = new TestWriteCommand('create', ['active_from' => $activeFrom, 'active_until' => $activeUntil]);
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        $this->assertViolationThrown($event, $activeFrom);
    }

    public static function invalidDateRangeProvider(): array
    {
        return [
            'active_from after active_until' => ['2025-12-31 23:59:59', '2025-01-01 00:00:00'],
            'same dates' => ['2025-06-15 12:00:00', '2025-06-15 12:00:00'],
            'same timestamp different format' => ['2025-01-01 00:00:00', '2025-01-01 00:00:00'],
        ];
    }

    public function testThrowsViolationOnUpdateWhenNewActiveFromAfterExistingActiveUntil(): void
    {
        $id = Uuid::randomHex();
        $command = new TestWriteCommand('update', ['active_from' => '2025-12-31 23:59:59'], $id);
        $event = $this->createEvent([$command]);

        $this->connection
            ->expects(static::once())
            ->method('fetchAllAssociative')
            ->willReturn([['id' => $id, 'active_from' => null, 'active_until' => '2025-01-01 00:00:00']]);

        $this->subscriber->validateTimeControl($event);

        $this->assertViolationThrown($event, '2025-12-31 23:59:59');
    }

    public function testThrowsViolationOnUpdateWhenNewActiveUntilBeforeExistingActiveFrom(): void
    {
        $id = Uuid::randomHex();
        $command = new TestWriteCommand('update', ['active_until' => '2025-01-01 00:00:00'], $id);
        $event = $this->createEvent([$command]);

        $this->connection
            ->expects(static::once())
            ->method('fetchAllAssociative')
            ->willReturn([['id' => $id, 'active_from' => '2025-12-31 23:59:59', 'active_until' => null]]);

        $this->subscriber->validateTimeControl($event);

        $this->assertViolationThrown($event, '2025-12-31 23:59:59');
    }

    public function testViolationContainsCorrectMetadata(): void
    {
        $command = new TestWriteCommand('create', ['active_from' => '2025-12-31 23:59:59', 'active_until' => '2025-01-01 00:00:00']);
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        $exceptions = $event->getExceptions()->getExceptions();
        static::assertCount(1, $exceptions);

        $exception = $exceptions[0];
        static::assertInstanceOf(WriteConstraintViolationException::class, $exception);

        $violations = $exception->getViolations();
        static::assertCount(1, $violations);

        $violation = $violations[0];
        static::assertSame('/activeFrom', $violation->getPropertyPath());
        static::assertSame(Defaults::ERROR_CODE_TIME_CONTROL, $violation->getCode());
        static::assertSame('The "activeFrom" date must be before the "activeUntil" date.', $violation->getMessage());
    }

    public function testSilentlyIgnoresInvalidDateFormat(): void
    {
        $command = new TestWriteCommand('create', ['active_from' => 'not-a-date', 'active_until' => '2025-12-31 23:59:59']);
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testSilentlyIgnoresBothInvalidDateFormats(): void
    {
        $command = new TestWriteCommand('create', ['active_from' => 'invalid', 'active_until' => 'also-invalid']);
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testHandlesEmptyStringValues(): void
    {
        $command = new TestWriteCommand('create', ['active_from' => '', 'active_until' => '']);
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testValidatesMultipleCommandsInSingleEvent(): void
    {
        $validCommand = new TestWriteCommand('create', ['active_from' => '2025-01-01 00:00:00', 'active_until' => '2025-12-31 23:59:59']);
        $invalidCommand = new TestWriteCommand('create', ['active_from' => '2025-12-31 23:59:59', 'active_until' => '2025-01-01 00:00:00']);
        $event = $this->createEvent([$validCommand, $invalidCommand]);

        $this->subscriber->validateTimeControl($event);

        $exceptions = $event->getExceptions()->getExceptions();
        static::assertCount(1, $exceptions);
        static::assertInstanceOf(WriteConstraintViolationException::class, $exceptions[0]);
    }

    public function testSkipsDeleteCommand(): void
    {
        $command = new TestWriteCommand('delete', []);
        $event = $this->createEvent([$command]);

        $this->connection
            ->expects(static::once())
            ->method('fetchAllAssociative')
            ->willReturn([]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testSkipsOtherEntities(): void
    {
        $command = new class('create', ['active_from' => '2025-12-31', 'active_until' => '2025-01-01']) extends TestWriteCommand {
            public function getEntityName(): string { return 'product'; }
        };
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    // =====================================================
    // Helper Methods
    // =====================================================

    private function createEvent(array $commands): PreWriteValidationEvent
    {
        $writeException = new \Shopware\Core\Framework\DataAbstractionLayer\Write\WriteException();

        $writeContext = $this->createMock(WriteContext::class);
        $writeContext->method('getExceptions')->willReturn($writeException);

        return new PreWriteValidationEvent($writeContext, $commands);
    }

    private function assertViolationThrown(PreWriteValidationEvent $event, string $expectedInvalidValue, string $expectedPropertyPath = '/activeFrom'): void
    {
        $exceptions = $event->getExceptions()->getExceptions();
        static::assertCount(1, $exceptions);

        $exception = $exceptions[0];
        static::assertInstanceOf(WriteConstraintViolationException::class, $exception);

        $violations = $exception->getViolations();
        static::assertCount(1, $violations);

        $violation = $violations[0];
        static::assertSame($expectedPropertyPath, $violation->getPropertyPath());
        static::assertSame(Defaults::ERROR_CODE_TIME_CONTROL, $violation->getCode());
        static::assertSame($expectedInvalidValue, $violation->getInvalidValue());
    }
}

/**
 * @internal
 */
class TestWriteCommand extends WriteCommand
{
    private string $privilegeType;
    protected array $payload;
    protected array $primaryKey;
    protected string $entityName;
    protected EntityExistence $existence;
    protected string $path;

    public function __construct(string $privilegeType, array $payload, string $id = '')
    {
        $this->privilegeType = $privilegeType;
        $this->payload = $payload;
        $this->primaryKey = ['id' => $id ?: Uuid::randomHex()];
        $this->entityName = ElysiumSlidesDefinition::ENTITY_NAME;
        $this->existence = new EntityExistence(ElysiumSlidesDefinition::ENTITY_NAME, [], false, false, false, []);
        $this->path = '';
    }

    public function getEntityName(): string
    {
        return $this->entityName;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getPrimaryKey(): array
    {
        return $this->primaryKey;
    }

    public function getPrivilege(): ?string
    {
        return $this->privilegeType;
    }
}

/**
 * @internal
 */
class TranslationTestWriteCommand extends WriteCommand
{
    private string $privilegeType;
    protected array $payload;
    protected array $primaryKey;
    protected string $entityName;
    protected EntityExistence $existence;
    protected string $path;

    public function __construct(string $privilegeType, array $payload, string $id = '')
    {
        $this->privilegeType = $privilegeType;
        $this->payload = $payload;
        $this->primaryKey = ['id' => $id ?: Uuid::randomHex()];
        $this->entityName = ElysiumSlidesTranslationDefinition::ENTITY_NAME;
        $this->existence = new EntityExistence(ElysiumSlidesTranslationDefinition::ENTITY_NAME, [], false, false, false, []);
        $this->path = '';
    }

    public function getEntityName(): string
    {
        return $this->entityName;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getPrimaryKey(): array
    {
        return $this->primaryKey;
    }

    public function getPrivilege(): ?string
    {
        return $this->privilegeType;
    }
}
