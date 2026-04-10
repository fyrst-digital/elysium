<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Subscriber;

use Blur\BlurElysiumSlider\Defaults;
use Blur\BlurElysiumSlider\Subscriber\CmsValidationSubscriber;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\InsertCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\JsonUpdateCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\UpdateCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\Validation\WriteConstraintViolationException;
use Shopware\Core\Framework\Uuid\Uuid;


class CmsValidationSubscriberTest extends TestCase
{
    private Connection&MockObject $connection;

    private CmsValidationSubscriber $subscriber;

    protected function setUp(): void
    {
        Feature::registerFeatures([
            'elysium_preview_time_control' => ['default' => true],
        ]);

        $this->connection = $this->createMock(Connection::class);
        $this->subscriber = new CmsValidationSubscriber($this->connection);
    }

    protected function tearDown(): void
    {
        Feature::resetRegisteredFeatures();
    }

    public function testGetSubscribedEvents(): void
    {
        $events = CmsValidationSubscriber::getSubscribedEvents();

        static::assertArrayHasKey(PreWriteValidationEvent::class, $events);
        static::assertIsArray($events[PreWriteValidationEvent::class]);

        $listenerMethods = array_column($events[PreWriteValidationEvent::class], 0);
        static::assertContains('validateTimeControl', $listenerMethods);
    }

    // =====================================================
    // InsertCommand Tests
    // =====================================================

    #[DataProvider('validDateRangeProvider')]
    public function testInsertSectionWithValidDates(string $activeFrom, string $activeUntil): void
    {
        $command = $this->createInsertCommand(
            CmsSectionDefinition::ENTITY_NAME,
            [
                'id' => Uuid::randomHex(),
                'type' => 'fullwidth',
                'custom_fields' => \json_encode([
                    Defaults::CMS_SECTION_SETTINGS_KEY => [
                        'activeFrom' => $activeFrom,
                        'activeUntil' => $activeUntil,
                    ],
                ]),
            ]
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    #[DataProvider('invalidDateRangeProvider')]
    public function testInsertSectionThrowsViolationOnInvalidDateRange(string $activeFrom, string $activeUntil): void
    {
        $command = $this->createInsertCommand(
            CmsSectionDefinition::ENTITY_NAME,
            [
                'id' => Uuid::randomHex(),
                'type' => 'fullwidth',
                'custom_fields' => \json_encode([
                    Defaults::CMS_SECTION_SETTINGS_KEY => [
                        'activeFrom' => $activeFrom,
                        'activeUntil' => $activeUntil,
                    ],
                ]),
            ]
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        $this->assertViolationThrown($event, $activeFrom);
    }

    #[DataProvider('validDateRangeProvider')]
    public function testInsertBlockWithValidDates(string $activeFrom, string $activeUntil): void
    {
        $command = $this->createInsertCommand(
            CmsBlockDefinition::ENTITY_NAME,
            [
                'id' => Uuid::randomHex(),
                'type' => 'blur-elysium-slider',
                'custom_fields' => \json_encode([
                    Defaults::CMS_BLOCK_ADVANCED_KEY => [
                        'activeFrom' => $activeFrom,
                        'activeUntil' => $activeUntil,
                    ],
                ]),
            ]
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    #[DataProvider('invalidDateRangeProvider')]
    public function testInsertBlockThrowsViolationOnInvalidDateRange(string $activeFrom, string $activeUntil): void
    {
        $command = $this->createInsertCommand(
            CmsBlockDefinition::ENTITY_NAME,
            [
                'id' => Uuid::randomHex(),
                'type' => 'blur-elysium-slider',
                'custom_fields' => \json_encode([
                    Defaults::CMS_BLOCK_ADVANCED_KEY => [
                        'activeFrom' => $activeFrom,
                        'activeUntil' => $activeUntil,
                    ],
                ]),
            ]
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        $this->assertViolationThrown($event, $activeFrom);
    }

    public function testInsertSkipsWhenNoCustomFields(): void
    {
        $command = $this->createInsertCommand(
            CmsSectionDefinition::ENTITY_NAME,
            [
                'id' => Uuid::randomHex(),
                'type' => 'fullwidth',
            ]
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testInsertSkipsWhenOnlyActiveFromIsSet(): void
    {
        $command = $this->createInsertCommand(
            CmsSectionDefinition::ENTITY_NAME,
            [
                'id' => Uuid::randomHex(),
                'type' => 'fullwidth',
                'custom_fields' => \json_encode([
                    Defaults::CMS_SECTION_SETTINGS_KEY => [
                        'activeFrom' => '2025-01-01 00:00:00',
                    ],
                ]),
            ]
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testInsertSkipsWhenOnlyActiveUntilIsSet(): void
    {
        $command = $this->createInsertCommand(
            CmsSectionDefinition::ENTITY_NAME,
            [
                'id' => Uuid::randomHex(),
                'type' => 'fullwidth',
                'custom_fields' => \json_encode([
                    Defaults::CMS_SECTION_SETTINGS_KEY => [
                        'activeUntil' => '2025-12-31 23:59:59',
                    ],
                ]),
            ]
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testInsertHandlesEmptyStringDates(): void
    {
        $command = $this->createInsertCommand(
            CmsSectionDefinition::ENTITY_NAME,
            [
                'id' => Uuid::randomHex(),
                'type' => 'fullwidth',
                'custom_fields' => \json_encode([
                    Defaults::CMS_SECTION_SETTINGS_KEY => [
                        'activeFrom' => '',
                        'activeUntil' => '',
                    ],
                ]),
            ]
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testInsertViolationContainsCorrectMetadata(): void
    {
        $command = $this->createInsertCommand(
            CmsSectionDefinition::ENTITY_NAME,
            [
                'id' => Uuid::randomHex(),
                'type' => 'fullwidth',
                'custom_fields' => \json_encode([
                    Defaults::CMS_SECTION_SETTINGS_KEY => [
                        'activeFrom' => '2025-12-31 23:59:59',
                        'activeUntil' => '2025-01-01 00:00:00',
                    ],
                ]),
            ]
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        $exceptions = $event->getExceptions()->getExceptions();
        static::assertCount(1, $exceptions);

        $violation = $exceptions[0]->getViolations()[0];
        static::assertSame('/customFields/elysiumSectionSettings/activeFrom', $violation->getPropertyPath());
        static::assertSame(Defaults::ERROR_CODE_TIME_CONTROL, $violation->getCode());
        static::assertSame('2025-12-31 23:59:59', $violation->getInvalidValue());
    }

    // =====================================================
    // JsonUpdateCommand Tests
    // =====================================================

    #[DataProvider('validDateRangeProvider')]
    public function testJsonUpdateSectionWithBothFieldsValid(string $activeFrom, string $activeUntil): void
    {
        $command = $this->createJsonUpdateCommand(
            CmsSectionDefinition::ENTITY_NAME,
            Uuid::randomHex(),
            [
                Defaults::CMS_SECTION_SETTINGS_KEY => [
                    'activeFrom' => $activeFrom,
                    'activeUntil' => $activeUntil,
                ],
            ]
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    #[DataProvider('invalidDateRangeProvider')]
    public function testJsonUpdateSectionThrowsViolationWhenBothFieldsInvalid(string $activeFrom, string $activeUntil): void
    {
        $command = $this->createJsonUpdateCommand(
            CmsSectionDefinition::ENTITY_NAME,
            Uuid::randomHex(),
            [
                Defaults::CMS_SECTION_SETTINGS_KEY => [
                    'activeFrom' => $activeFrom,
                    'activeUntil' => $activeUntil,
                ],
            ]
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        $this->assertViolationThrown($event, $activeFrom);
    }

    public function testJsonUpdateMergesPartialActiveFromWithExistingActiveUntil(): void
    {
        $id = Uuid::randomHex();
        $command = $this->createJsonUpdateCommand(
            CmsSectionDefinition::ENTITY_NAME,
            $id,
            [
                Defaults::CMS_SECTION_SETTINGS_KEY => [
                    'activeFrom' => '2025-01-01 00:00:00',
                ],
            ]
        );
        $event = $this->createEvent([$command]);

        $this->connection
            ->expects(static::once())
            ->method('fetchAssociative')
            ->willReturn([
                'custom_fields' => \json_encode([
                    Defaults::CMS_SECTION_SETTINGS_KEY => [
                        'activeFrom' => null,
                        'activeUntil' => '2025-12-31 23:59:59',
                    ],
                ]),
            ]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testJsonUpdateMergesPartialActiveUntilWithExistingActiveFrom(): void
    {
        $id = Uuid::randomHex();
        $command = $this->createJsonUpdateCommand(
            CmsSectionDefinition::ENTITY_NAME,
            $id,
            [
                Defaults::CMS_SECTION_SETTINGS_KEY => [
                    'activeUntil' => '2025-12-31 23:59:59',
                ],
            ]
        );
        $event = $this->createEvent([$command]);

        $this->connection
            ->expects(static::once())
            ->method('fetchAssociative')
            ->willReturn([
                'custom_fields' => \json_encode([
                    Defaults::CMS_SECTION_SETTINGS_KEY => [
                        'activeFrom' => '2025-01-01 00:00:00',
                        'activeUntil' => null,
                    ],
                ]),
            ]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testJsonUpdateDetectsConflictWithExistingValue(): void
    {
        $id = Uuid::randomHex();
        $command = $this->createJsonUpdateCommand(
            CmsSectionDefinition::ENTITY_NAME,
            $id,
            [
                Defaults::CMS_SECTION_SETTINGS_KEY => [
                    'activeFrom' => '2025-12-31 23:59:59',
                ],
            ]
        );
        $event = $this->createEvent([$command]);

        $this->connection
            ->expects(static::once())
            ->method('fetchAssociative')
            ->willReturn([
                'custom_fields' => \json_encode([
                    Defaults::CMS_SECTION_SETTINGS_KEY => [
                        'activeFrom' => null,
                        'activeUntil' => '2025-01-01 00:00:00',
                    ],
                ]),
            ]);

        $this->subscriber->validateTimeControl($event);

        $this->assertViolationThrown($event, '2025-12-31 23:59:59');
    }

    public function testJsonUpdateSkipsWhenSettingsKeyNotInPayload(): void
    {
        $command = $this->createJsonUpdateCommand(
            CmsSectionDefinition::ENTITY_NAME,
            Uuid::randomHex(),
            [
                'someOtherKey' => ['foo' => 'bar'],
            ]
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testJsonUpdateSkipsWhenStorageNameIsNotCustomFields(): void
    {
        $command = $this->createJsonUpdateCommand(
            CmsSectionDefinition::ENTITY_NAME,
            Uuid::randomHex(),
            [
                Defaults::CMS_SECTION_SETTINGS_KEY => [
                    'activeFrom' => '2025-12-31 23:59:59',
                    'activeUntil' => '2025-01-01 00:00:00',
                ],
            ],
            'other_json_field'
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testJsonUpdateBlockThrowsViolation(): void
    {
        $command = $this->createJsonUpdateCommand(
            CmsBlockDefinition::ENTITY_NAME,
            Uuid::randomHex(),
            [
                Defaults::CMS_BLOCK_ADVANCED_KEY => [
                    'activeFrom' => '2025-12-31 23:59:59',
                    'activeUntil' => '2025-01-01 00:00:00',
                ],
            ]
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        $this->assertViolationThrown($event, '2025-12-31 23:59:59');
    }

    // =====================================================
    // Skipped Command Tests
    // =====================================================

    public function testSkipsRegularUpdateCommand(): void
    {
        $command = new CmsTestUpdateCommand(
            CmsBlockDefinition::ENTITY_NAME,
            ['position' => 0, 'type' => 'blur-elysium-slider', 'name' => 'Test'],
            Uuid::randomHex()
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    public function testSkipsOtherEntities(): void
    {
        $command = $this->createInsertCommand(
            'product',
            [
                'id' => Uuid::randomHex(),
                'custom_fields' => \json_encode([
                    Defaults::CMS_SECTION_SETTINGS_KEY => [
                        'activeFrom' => '2025-12-31 23:59:59',
                        'activeUntil' => '2025-01-01 00:00:00',
                    ],
                ]),
            ]
        );
        $event = $this->createEvent([$command]);

        $this->subscriber->validateTimeControl($event);

        static::assertCount(0, $event->getExceptions()->getExceptions());
    }

    // =====================================================
    // Cross-entity Tests
    // =====================================================

    public function testValidatesBothSectionAndBlockInSameEvent(): void
    {
        $sectionCommand = $this->createInsertCommand(
            CmsSectionDefinition::ENTITY_NAME,
            [
                'id' => Uuid::randomHex(),
                'type' => 'fullwidth',
                'custom_fields' => \json_encode([
                    Defaults::CMS_SECTION_SETTINGS_KEY => [
                        'activeFrom' => '2025-01-01 00:00:00',
                        'activeUntil' => '2025-12-31 23:59:59',
                    ],
                ]),
            ]
        );
        $blockCommand = $this->createInsertCommand(
            CmsBlockDefinition::ENTITY_NAME,
            [
                'id' => Uuid::randomHex(),
                'type' => 'blur-elysium-slider',
                'custom_fields' => \json_encode([
                    Defaults::CMS_BLOCK_ADVANCED_KEY => [
                        'activeFrom' => '2025-12-31 23:59:59',
                        'activeUntil' => '2025-01-01 00:00:00',
                    ],
                ]),
            ]
        );
        $event = $this->createEvent([$sectionCommand, $blockCommand]);

        $this->subscriber->validateTimeControl($event);

        $exceptions = $event->getExceptions()->getExceptions();
        static::assertCount(1, $exceptions);
        static::assertInstanceOf(WriteConstraintViolationException::class, $exceptions[0]);
    }

    // =====================================================
    // Data Providers
    // =====================================================

    public static function validDateRangeProvider(): array
    {
        return [
            'standard range' => ['2025-01-01 00:00:00', '2025-12-31 23:59:59'],
            'one second difference' => ['2025-06-15 12:00:00', '2025-06-15 12:00:01'],
            'one day difference' => ['2025-01-01 00:00:00', '2025-01-02 00:00:00'],
        ];
    }

    public static function invalidDateRangeProvider(): array
    {
        return [
            'active_from after active_until' => ['2025-12-31 23:59:59', '2025-01-01 00:00:00'],
            'same dates' => ['2025-06-15 12:00:00', '2025-06-15 12:00:00'],
        ];
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

    private function createInsertCommand(string $entityName, array $payload): CmsTestInsertCommand
    {
        return new CmsTestInsertCommand($entityName, $payload);
    }

    private function createJsonUpdateCommand(
        string $entityName,
        string $id,
        array $payload,
        string $storageName = 'custom_fields'
    ): CmsTestJsonUpdateCommand {
        return new CmsTestJsonUpdateCommand($entityName, $id, $payload, $storageName);
    }

    private function assertViolationThrown(PreWriteValidationEvent $event, string $expectedInvalidValue): void
    {
        $exceptions = $event->getExceptions()->getExceptions();
        static::assertGreaterThanOrEqual(1, \count($exceptions));

        $violation = $exceptions[0]->getViolations()[0];
        static::assertSame(Defaults::ERROR_CODE_TIME_CONTROL, $violation->getCode());
        static::assertSame($expectedInvalidValue, $violation->getInvalidValue());
    }
}

/**
 * Mock InsertCommand that bypasses parent constructor to avoid EntityDefinition initialization.
 * @internal
 */
class CmsTestInsertCommand extends WriteCommand
{
    private string $privilegeType = 'create';
    protected array $payload;
    protected array $primaryKey;
    protected string $entityName;

    public function __construct(string $entityName, array $payload)
    {
        $this->entityName = $entityName;
        $this->payload = $payload;
        $this->primaryKey = ['id' => $payload['id'] ?? Uuid::randomHex()];
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

    public function getPath(): string
    {
        return '';
    }
}

/**
 * Mock JsonUpdateCommand that bypasses parent constructor.
 * @internal
 */
class CmsTestJsonUpdateCommand extends WriteCommand
{
    private string $privilegeType = 'update';
    protected array $payload;
    protected array $primaryKey;
    protected string $entityName;
    private string $storageName;

    public function __construct(string $entityName, string $id, array $payload, string $storageName)
    {
        $this->entityName = $entityName;
        $this->payload = $payload;
        $this->primaryKey = ['id' => $id];
        $this->storageName = $storageName;
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

    public function getStorageName(): string
    {
        return $this->storageName;
    }

    public function getPath(): string
    {
        return '';
    }
}

/**
 * Mock UpdateCommand that bypasses parent constructor.
 * @internal
 */
class CmsTestUpdateCommand extends WriteCommand
{
    private string $privilegeType = 'update';
    protected array $payload;
    protected array $primaryKey;
    protected string $entityName;

    public function __construct(string $entityName, array $payload, string $id)
    {
        $this->entityName = $entityName;
        $this->payload = $payload;
        $this->primaryKey = ['id' => $id];
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

    public function getPath(): string
    {
        return '';
    }
}
