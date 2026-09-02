<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1781000000ConsolidateContentSettings;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\DataProvider;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1781000000ConsolidateContentSettingsTest extends AbstractMigrationTestCase
{
    /**
     * @var array<string, string>
     */
    private const MEDIA_KEYS = [
        'desktop' => 'slide_cover_id',
        'mobile' => 'slide_cover_mobile_id',
        'tablet' => 'slide_cover_tablet_id',
        'video' => 'slide_cover_video_id',
        'focus' => 'presentation_media_id',
    ];

    public function testMigrationRunsOnFreshInstall(): void
    {
        $this->createSlideTable();
        $this->createFreshInstallTranslationTable();

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $this->assertTableExists('blur_elysium_slides');
        $this->assertTableExists('blur_elysium_slides_translation');
        $this->assertColumnExists('blur_elysium_slides_translation', 'content_settings');
    }

    public function testMigrationMigratesTranslationData(): void
    {
        $this->createSlideTable();
        $this->createUpgradeTranslationTable();

        $slideId = $this->insertSlide([]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);

        $this->insertTranslation($slideId, $languageId, [
            'name' => 'Test Slide',
            'title' => 'Test Title',
            'description' => '<p>Test Description</p>',
            'button_label' => 'Click Me',
            'url' => '/test-url',
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'title');
        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'description');
        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'button_label');
        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'url');

        $decoded = $this->fetchContentSettings($slideId, $languageId);
        static::assertSame('Test Title', $decoded['title']);
        static::assertSame('<p>Test Description</p>', $decoded['description']);
        static::assertSame('Click Me', $decoded['button']['label']);
        static::assertSame('/test-url', $decoded['url']);
    }

    public function testMigrationMigratesMediaIds(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createFreshInstallTranslationTable();

        $desktopCoverId = Uuid::randomBytes();
        $mobileCoverId = Uuid::randomBytes();
        $tabletCoverId = Uuid::randomBytes();
        $videoCoverId = Uuid::randomBytes();
        $focusImageId = Uuid::randomBytes();

        $slideId = $this->insertSlide([
            'slide_cover_id' => $desktopCoverId,
            'slide_cover_mobile_id' => $mobileCoverId,
            'slide_cover_tablet_id' => $tabletCoverId,
            'slide_cover_video_id' => $videoCoverId,
            'presentation_media_id' => $focusImageId,
        ]);

        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->insertTranslation($slideId, $languageId, ['name' => 'Test Slide']);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $this->assertAllMediaColumnsDropped();

        $decoded = $this->fetchContentSettings($slideId, $languageId);
        static::assertSame(Uuid::fromBytesToHex($desktopCoverId), $decoded['slideCover']['desktopId']);
        static::assertSame(Uuid::fromBytesToHex($mobileCoverId), $decoded['slideCover']['mobileId']);
        static::assertSame(Uuid::fromBytesToHex($tabletCoverId), $decoded['slideCover']['tabletId']);
        static::assertSame(Uuid::fromBytesToHex($videoCoverId), $decoded['slideCover']['videoId']);
        static::assertSame(Uuid::fromBytesToHex($focusImageId), $decoded['focusImageId']);
    }

    public function testMigrationMigratesCombinedTextAndMedia(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createUpgradeTranslationTable();

        $desktopCoverId = Uuid::randomBytes();
        $focusImageId = Uuid::randomBytes();
        $slideId = $this->insertSlide([
            'slide_cover_id' => $desktopCoverId,
            'presentation_media_id' => $focusImageId,
        ]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);

        $this->insertTranslation($slideId, $languageId, [
            'name' => 'Combined Slide',
            'title' => 'Headline',
            'description' => '<p>Body</p>',
            'button_label' => 'Go',
            'url' => '/go',
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'title');
        $this->assertAllMediaColumnsDropped();

        $decoded = $this->fetchContentSettings($slideId, $languageId);
        static::assertSame('Headline', $decoded['title']);
        static::assertSame('<p>Body</p>', $decoded['description']);
        static::assertSame('Go', $decoded['button']['label']);
        static::assertSame('/go', $decoded['url']);
        static::assertSame(Uuid::fromBytesToHex($desktopCoverId), $decoded['slideCover']['desktopId']);
        static::assertSame(Uuid::fromBytesToHex($focusImageId), $decoded['focusImageId']);
    }

    public function testMigrationCopiesMediaToEveryTranslationAndKeepsLanguageSpecificCopy(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createUpgradeTranslationTable();

        $desktopCoverId = Uuid::randomBytes();
        $slideId = $this->insertSlide([
            'slide_cover_id' => $desktopCoverId,
        ]);

        $systemLanguageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $otherLanguageId = Uuid::randomBytes();

        $this->insertTranslation($slideId, $systemLanguageId, [
            'name' => 'EN Slide',
            'title' => 'English title',
        ]);
        $this->insertTranslation($slideId, $otherLanguageId, [
            'name' => 'DE Slide',
            'title' => 'German title',
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $coverHex = Uuid::fromBytesToHex($desktopCoverId);

        $en = $this->fetchContentSettings($slideId, $systemLanguageId);
        static::assertSame('English title', $en['title']);
        static::assertSame($coverHex, $en['slideCover']['desktopId']);

        $de = $this->fetchContentSettings($slideId, $otherLanguageId);
        static::assertSame('German title', $de['title']);
        static::assertSame($coverHex, $de['slideCover']['desktopId']);
    }

    public function testMigrationCreatesSystemLanguageTranslationWhenOnlyOtherLanguageExists(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createFreshInstallTranslationTable();

        $desktopCoverId = Uuid::randomBytes();
        $slideId = $this->insertSlide([
            'slide_cover_id' => $desktopCoverId,
        ]);

        $otherLanguageId = Uuid::randomBytes();
        $this->insertTranslation($slideId, $otherLanguageId, [
            'name' => 'German only',
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $systemLanguageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $coverHex = Uuid::fromBytesToHex($desktopCoverId);

        $systemName = $this->connection->fetchOne(
            'SELECT name FROM blur_elysium_slides_translation WHERE blur_elysium_slides_id = ? AND language_id = ?',
            [$slideId, $systemLanguageId]
        );
        static::assertSame('German only', $systemName);

        $systemSettings = $this->fetchContentSettings($slideId, $systemLanguageId);
        static::assertSame($coverHex, $systemSettings['slideCover']['desktopId']);

        $otherSettings = $this->fetchContentSettings($slideId, $otherLanguageId);
        static::assertSame($coverHex, $otherSettings['slideCover']['desktopId']);
        $this->assertAllMediaColumnsDropped();
    }

    public function testMigrationCreatesSystemLanguageTranslationWhenSlideHasNoTranslations(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createFreshInstallTranslationTable();

        $desktopCoverId = Uuid::randomBytes();
        $slideId = $this->insertSlide([
            'slide_cover_id' => $desktopCoverId,
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $systemLanguageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $name = $this->connection->fetchOne(
            'SELECT name FROM blur_elysium_slides_translation WHERE blur_elysium_slides_id = ? AND language_id = ?',
            [$slideId, $systemLanguageId]
        );
        static::assertSame(Uuid::fromBytesToHex($slideId), $name);

        $decoded = $this->fetchContentSettings($slideId, $systemLanguageId);
        static::assertSame(Uuid::fromBytesToHex($desktopCoverId), $decoded['slideCover']['desktopId']);
        $this->assertAllMediaColumnsDropped();
    }

    public function testMigrationMigratesPresentationMediaWithoutSlideCoverColumn(): void
    {
        $this->createSlideTable([
            '`presentation_media_id` BINARY(16) NULL',
        ]);
        $this->createFreshInstallTranslationTable();

        $focusImageId = Uuid::randomBytes();
        $slideId = $this->insertSlide([
            'presentation_media_id' => $focusImageId,
        ]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->insertTranslation($slideId, $languageId, ['name' => 'Focus only']);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $this->assertColumnDoesNotExist('blur_elysium_slides', 'presentation_media_id');

        $decoded = $this->fetchContentSettings($slideId, $languageId);
        static::assertSame(Uuid::fromBytesToHex($focusImageId), $decoded['focusImageId']);
    }

    public function testMigrationKeepsExistingContentSettingsWhenLegacyTitleIsEmpty(): void
    {
        $this->createSlideTable();
        $this->createUpgradeTranslationTable();

        $slideId = $this->insertSlide([]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);

        $this->insertTranslation($slideId, $languageId, [
            'name' => 'Prefilled',
            'title' => null,
            'content_settings' => json_encode(['title' => 'Already in JSON'], \JSON_THROW_ON_ERROR),
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $decoded = $this->fetchContentSettings($slideId, $languageId);
        static::assertSame('Already in JSON', $decoded['title']);
    }

    public function testMigrationMigratesZeroStringTitle(): void
    {
        $this->createSlideTable();
        $this->createUpgradeTranslationTable();

        $slideId = $this->insertSlide([]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);

        $this->insertTranslation($slideId, $languageId, [
            'name' => 'Zero title',
            'title' => '0',
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $decoded = $this->fetchContentSettings($slideId, $languageId);
        static::assertSame('0', $decoded['title']);
    }

    public function testMigrationPreservesProductAndCategoryForeignKeys(): void
    {
        $this->createSlideTableWithProductCategoryAndMediaForeignKeys();
        $this->createFreshInstallTranslationTable();

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);

        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_id');
        $this->assertForeignKeyExists('blur_elysium_slides', 'fk.blur_elysium_slides.product_id');
        $this->assertForeignKeyExists('blur_elysium_slides', 'fk.blur_elysium_slides.category_id');
        $this->assertForeignKeyDoesNotExist('blur_elysium_slides', 'fk.blur_elysium_slides.slide_cover_id');
        $this->assertForeignKeyDoesNotExist('blur_elysium_slides', 'fk.blur_elysium_slides.slide_cover_mobile_id');
    }

    public function testMigrationIsIdempotent(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createUpgradeTranslationTable();

        $slideId = $this->insertSlide([
            'slide_cover_id' => Uuid::randomBytes(),
        ]);

        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->insertTranslation($slideId, $languageId, [
            'name' => 'Test Slide',
            'title' => 'Test Title',
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'title');
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_id');
    }

    public function testVerifyFailureDoesNotDropMediaColumns(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createFreshInstallTranslationTable();

        $slideId = $this->insertSlide([
            'slide_cover_id' => Uuid::randomBytes(),
        ]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->insertTranslation($slideId, $languageId, ['name' => 'Unmigrated']);

        $migration = new class extends Migration1781000000ConsolidateContentSettings {
            protected function migrateMediaIds(Connection $connection): void
            {
            }
        };

        try {
            $this->runMigration($migration);
            static::fail('Expected contentSettings verification to fail');
        } catch (\RuntimeException $e) {
            static::assertStringContainsString('verification failed', $e->getMessage());
        }

        $this->assertColumnExists('blur_elysium_slides', 'slide_cover_id');
    }

    /**
     * @param array<string, mixed> $case
     */
    #[DataProvider('contentSettingsMigrationCases')]
    public function testContentSettingsMigrationCases(array $case): void
    {
        $schema = $this->setUpSmokeSchema($case['schema'] ?? 'authentic49');

        $slideId = Uuid::randomBytes();
        $tokens = ['%slideHex%' => Uuid::fromBytesToHex($slideId)];
        $mediaBytes = [];

        foreach (array_keys(self::MEDIA_KEYS) as $key) {
            $mediaBytes[$key] = Uuid::randomBytes();
            $tokens['%' . $key . '%'] = Uuid::fromBytesToHex($mediaBytes[$key]);
            $tokens['%existing' . ucfirst($key) . '%'] = Uuid::fromBytesToHex(Uuid::randomBytes());
        }

        $languages = [
            'system' => Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM),
            'other' => Uuid::randomBytes(),
            'other2' => Uuid::randomBytes(),
        ];

        $slideData = ['id' => $slideId];
        foreach ($case['slideMedia'] ?? [] as $key) {
            static::assertArrayHasKey($key, $mediaBytes);
            $slideData[self::MEDIA_KEYS[$key]] = $mediaBytes[$key];
        }
        $this->insertSlide($slideData);

        foreach ($case['translations'] ?? [] as $translation) {
            $langKey = $translation['lang'] ?? 'system';
            static::assertArrayHasKey($langKey, $languages);

            $row = ['name' => $translation['name'] ?? 'Slide'];
            foreach (['title', 'description', 'button_label', 'url', 'created_at'] as $field) {
                if (\array_key_exists($field, $translation)) {
                    $row[$field] = $translation[$field];
                }
            }

            if (\array_key_exists('content_settings', $translation)) {
                $contentSettings = $this->interpolate($translation['content_settings'], $tokens);
                $row['content_settings'] = \is_array($contentSettings)
                    ? json_encode($contentSettings, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES)
                    : (string) $contentSettings;
            }

            $this->insertTranslation($slideId, $languages[$langKey], $row);
        }

        $this->runMigration(new Migration1781000000ConsolidateContentSettings());

        $this->assertColumnExists('blur_elysium_slides_translation', 'content_settings');

        if ($schema['hasText']) {
            $this->assertLegacyTextColumnsDropped();
        }

        foreach ($schema['mediaColumns'] as $column) {
            $this->assertColumnDoesNotExist('blur_elysium_slides', $column);
        }

        foreach ($case['expectedNull'] ?? [] as $langKey) {
            $this->assertContentSettingsNull($slideId, $languages[$langKey]);
        }

        foreach ($case['expected'] ?? [] as $langKey => $expected) {
            static::assertArrayHasKey($langKey, $languages);
            $decoded = $this->fetchContentSettings($slideId, $languages[$langKey]);
            $interpolated = $this->interpolate($expected, $tokens);
            static::assertIsArray($interpolated);
            $this->assertContentSettingsMatch($interpolated, $decoded);
        }

        if (isset($case['expectSystemName'])) {
            $name = $this->connection->fetchOne(
                'SELECT name FROM blur_elysium_slides_translation WHERE blur_elysium_slides_id = ? AND language_id = ?',
                [$slideId, $languages['system']]
            );
            $expectedName = $this->interpolate($case['expectSystemName'], $tokens);
            static::assertIsString($expectedName);
            static::assertSame($expectedName, $name);
        }
    }

    /**
     * @return iterable<string, array<int, array<string, mixed>>>
     */
    public static function contentSettingsMigrationCases(): iterable
    {
        $longDescription = str_repeat('Lorem ipsum dolor sit amet. ', 400);

        yield 'all four text fields' => [[
            'translations' => [[
                'lang' => 'system',
                'name' => 'Full text',
                'title' => 'Headline',
                'description' => '<p>Body</p>',
                'button_label' => 'Go',
                'url' => '/go',
            ]],
            'expected' => [
                'system' => [
                    'title' => 'Headline',
                    'description' => '<p>Body</p>',
                    'button' => ['label' => 'Go'],
                    'url' => '/go',
                ],
            ],
        ]];

        yield 'title only' => [[
            'translations' => [[
                'lang' => 'system',
                'name' => 'Title only',
                'title' => 'Only title',
            ]],
            'expected' => [
                'system' => ['title' => 'Only title'],
            ],
        ]];

        yield 'description only' => [[
            'translations' => [[
                'lang' => 'system',
                'name' => 'Description only',
                'description' => '<p>Only body</p>',
            ]],
            'expected' => [
                'system' => ['description' => '<p>Only body</p>'],
            ],
        ]];

        yield 'button only' => [[
            'translations' => [[
                'lang' => 'system',
                'name' => 'Button only',
                'button_label' => 'Only button',
            ]],
            'expected' => [
                'system' => ['button' => ['label' => 'Only button']],
            ],
        ]];

        yield 'url only' => [[
            'translations' => [[
                'lang' => 'system',
                'name' => 'URL only',
                'url' => '/only-url',
            ]],
            'expected' => [
                'system' => ['url' => '/only-url'],
            ],
        ]];

        yield 'all text fields null' => [[
            'translations' => [[
                'lang' => 'system',
                'name' => 'All null',
            ]],
            'expectedNull' => ['system'],
        ]];

        yield 'empty strings are skipped' => [[
            'translations' => [[
                'lang' => 'system',
                'name' => 'Empty strings',
                'title' => '',
                'description' => '',
                'button_label' => '',
                'url' => '',
            ]],
            'expectedNull' => ['system'],
        ]];

        yield 'zero string title is kept' => [[
            'translations' => [[
                'lang' => 'system',
                'name' => 'Zero',
                'title' => '0',
            ]],
            'expected' => [
                'system' => ['title' => '0'],
            ],
        ]];

        yield 'unicode and emoji' => [[
            'translations' => [[
                'lang' => 'system',
                'name' => 'Unicode',
                'title' => '标题 🎉 café',
                'button_label' => 'Käufen',
            ]],
            'expected' => [
                'system' => [
                    'title' => '标题 🎉 café',
                    'button' => ['label' => 'Käufen'],
                ],
            ],
        ]];

        yield 'html description' => [[
            'translations' => [[
                'lang' => 'system',
                'name' => 'HTML',
                'description' => '<p>Hello <strong>World</strong></p>',
            ]],
            'expected' => [
                'system' => ['description' => '<p>Hello <strong>World</strong></p>'],
            ],
        ]];

        yield 'url with query and fragment' => [[
            'translations' => [[
                'lang' => 'system',
                'name' => 'Query URL',
                'url' => '/path?foo=1&bar=2#frag',
            ]],
            'expected' => [
                'system' => ['url' => '/path?foo=1&bar=2#frag'],
            ],
        ]];

        yield 'url with quotes and slashes' => [[
            'translations' => [[
                'lang' => 'system',
                'name' => 'Quoted URL',
                'url' => '/say/"hi"/test',
            ]],
            'expected' => [
                'system' => ['url' => '/say/"hi"/test'],
            ],
        ]];

        yield 'long description' => [[
            'translations' => [[
                'lang' => 'system',
                'name' => 'Long',
                'description' => $longDescription,
            ]],
            'expected' => [
                'system' => ['description' => $longDescription],
            ],
        ]];

        yield 'empty json plus legacy text' => [[
            'schema' => 'authentic49Json',
            'translations' => [[
                'lang' => 'system',
                'name' => 'Empty JSON',
                'title' => 'From column',
                'content_settings' => '{}',
            ]],
            'expected' => [
                'system' => ['title' => 'From column'],
            ],
        ]];

        yield 'empty legacy title keeps json title' => [[
            'schema' => 'authentic49Json',
            'translations' => [[
                'lang' => 'system',
                'name' => 'Keep JSON',
                'title' => '',
                'content_settings' => ['title' => 'Already in JSON'],
            ]],
            'expected' => [
                'system' => ['title' => 'Already in JSON'],
            ],
        ]];

        yield 'legacy title overwrites json title' => [[
            'schema' => 'authentic49Json',
            'translations' => [[
                'lang' => 'system',
                'name' => 'Overwrite',
                'title' => 'Legacy title',
                'content_settings' => ['title' => 'JSON title', 'url' => '/keep'],
            ]],
            'expected' => [
                'system' => [
                    'title' => 'Legacy title',
                    'url' => '/keep',
                ],
            ],
        ]];

        yield 'preserves slide cover alt and title' => [[
            'schema' => 'authentic49Json',
            'slideMedia' => ['desktop'],
            'translations' => [[
                'lang' => 'system',
                'name' => 'Cover meta',
                'content_settings' => [
                    'slideCover' => [
                        'alt' => 'Keep alt',
                        'title' => 'Keep title',
                        'desktopId' => '%existingDesktop%',
                    ],
                ],
            ]],
            'expected' => [
                'system' => [
                    'slideCover' => [
                        'alt' => 'Keep alt',
                        'title' => 'Keep title',
                        'desktopId' => '%desktop%',
                    ],
                ],
            ],
        ]];

        yield 'coerces string button to object' => [[
            'schema' => 'authentic49Json',
            'translations' => [[
                'lang' => 'system',
                'name' => 'Button coerce',
                'button_label' => 'Click',
                'content_settings' => ['button' => 'old string'],
            ]],
            'expected' => [
                'system' => ['button' => ['label' => 'Click']],
            ],
        ]];

        yield 'coerces string slideCover to object' => [[
            'schema' => 'authentic49Json',
            'slideMedia' => ['desktop'],
            'translations' => [[
                'lang' => 'system',
                'name' => 'Cover coerce',
                'content_settings' => ['slideCover' => 'not-an-object'],
            ]],
            'expected' => [
                'system' => [
                    'slideCover' => ['desktopId' => '%desktop%'],
                ],
            ],
        ]];

        yield 'all five media fks' => [[
            'slideMedia' => ['desktop', 'mobile', 'tablet', 'video', 'focus'],
            'translations' => [[
                'lang' => 'system',
                'name' => 'All media',
            ]],
            'expected' => [
                'system' => [
                    'slideCover' => [
                        'desktopId' => '%desktop%',
                        'mobileId' => '%mobile%',
                        'tabletId' => '%tablet%',
                        'videoId' => '%video%',
                    ],
                    'focusImageId' => '%focus%',
                ],
            ],
        ]];

        foreach (array_keys(self::MEDIA_KEYS) as $mediaKey) {
            yield 'only ' . $mediaKey . ' media fk' => [[
                'slideMedia' => [$mediaKey],
                'translations' => [[
                    'lang' => 'system',
                    'name' => 'Single media',
                ]],
                'expected' => [
                    'system' => self::mediaExpected($mediaKey),
                ],
            ]];
        }

        yield 'mix of set and null media fks' => [[
            'slideMedia' => ['desktop', 'focus'],
            'translations' => [[
                'lang' => 'system',
                'name' => 'Mixed media',
            ]],
            'expected' => [
                'system' => [
                    'slideCover' => ['desktopId' => '%desktop%'],
                    'focusImageId' => '%focus%',
                ],
            ],
        ]];

        yield 'null media fk does not wipe existing json media id' => [[
            'schema' => 'authentic49Json',
            'slideMedia' => ['mobile'],
            'translations' => [[
                'lang' => 'system',
                'name' => 'Keep desktop',
                'content_settings' => [
                    'slideCover' => ['desktopId' => '%existingDesktop%'],
                ],
            ]],
            'expected' => [
                'system' => [
                    'slideCover' => [
                        'desktopId' => '%existingDesktop%',
                        'mobileId' => '%mobile%',
                    ],
                ],
            ],
        ]];

        yield 'legacy media fk overwrites existing json media id' => [[
            'schema' => 'authentic49Json',
            'slideMedia' => ['desktop'],
            'translations' => [[
                'lang' => 'system',
                'name' => 'Overwrite media',
                'content_settings' => [
                    'slideCover' => ['desktopId' => '%existingDesktop%'],
                ],
            ]],
            'expected' => [
                'system' => [
                    'slideCover' => ['desktopId' => '%desktop%'],
                ],
            ],
        ]];

        yield 'two languages keep distinct titles and shared cover' => [[
            'slideMedia' => ['desktop'],
            'translations' => [
                [
                    'lang' => 'system',
                    'name' => 'EN',
                    'title' => 'English',
                ],
                [
                    'lang' => 'other',
                    'name' => 'DE',
                    'title' => 'German',
                ],
            ],
            'expected' => [
                'system' => [
                    'title' => 'English',
                    'slideCover' => ['desktopId' => '%desktop%'],
                ],
                'other' => [
                    'title' => 'German',
                    'slideCover' => ['desktopId' => '%desktop%'],
                ],
            ],
        ]];

        yield 'three languages' => [[
            'slideMedia' => ['desktop'],
            'translations' => [
                [
                    'lang' => 'system',
                    'name' => 'EN',
                    'title' => 'English',
                ],
                [
                    'lang' => 'other',
                    'name' => 'DE',
                    'title' => 'German',
                ],
                [
                    'lang' => 'other2',
                    'name' => 'FR',
                    'title' => 'French',
                ],
            ],
            'expected' => [
                'system' => [
                    'title' => 'English',
                    'slideCover' => ['desktopId' => '%desktop%'],
                ],
                'other' => [
                    'title' => 'German',
                    'slideCover' => ['desktopId' => '%desktop%'],
                ],
                'other2' => [
                    'title' => 'French',
                    'slideCover' => ['desktopId' => '%desktop%'],
                ],
            ],
        ]];

        yield 'creates system language from oldest other name' => [[
            'slideMedia' => ['desktop'],
            'translations' => [
                [
                    'lang' => 'other',
                    'name' => 'Newer',
                    'created_at' => '2024-06-01 00:00:00.000',
                ],
                [
                    'lang' => 'other2',
                    'name' => 'Oldest',
                    'created_at' => '2020-01-01 00:00:00.000',
                ],
            ],
            'expectSystemName' => 'Oldest',
            'expected' => [
                'system' => [
                    'slideCover' => ['desktopId' => '%desktop%'],
                ],
                'other' => [
                    'slideCover' => ['desktopId' => '%desktop%'],
                ],
                'other2' => [
                    'slideCover' => ['desktopId' => '%desktop%'],
                ],
            ],
        ]];

        yield 'creates system language named with slide hex when no translations' => [[
            'slideMedia' => ['desktop'],
            'translations' => [],
            'expectSystemName' => '%slideHex%',
            'expected' => [
                'system' => [
                    'slideCover' => ['desktopId' => '%desktop%'],
                ],
            ],
        ]];
    }

    /**
     * @return iterable<string, array{0: string, 1: string}>
     */
    public static function partialMediaSchemaCases(): iterable
    {
        foreach (self::MEDIA_KEYS as $mediaKey => $column) {
            yield $mediaKey => [$column, $mediaKey];
        }
    }

    #[DataProvider('partialMediaSchemaCases')]
    public function testPartialMediaSchemaMigratesSingleColumn(string $column, string $mediaKey): void
    {
        $this->createSlideTable(['`' . $column . '` BINARY(16) NULL']);
        $this->createAuthentic49TranslationTable();

        $mediaId = Uuid::randomBytes();
        $slideId = $this->insertSlide([$column => $mediaId]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->insertTranslation($slideId, $languageId, ['name' => 'Partial']);

        $this->runMigration(new Migration1781000000ConsolidateContentSettings());

        $this->assertColumnDoesNotExist('blur_elysium_slides', $column);
        $this->assertColumnExists('blur_elysium_slides_translation', 'content_settings');

        $decoded = $this->fetchContentSettings($slideId, $languageId);
        $this->assertContentSettingsMatch(
            $this->interpolate(self::mediaExpected($mediaKey), [
                '%' . $mediaKey . '%' => Uuid::fromBytesToHex($mediaId),
            ]),
            $decoded
        );
    }

    public function testLeftoverEmptySchemaDropsColumnsWithoutWritingJson(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createAuthentic49TranslationTable();

        $slideId = $this->insertSlide([]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->insertTranslation($slideId, $languageId, ['name' => 'Empty leftover']);

        $this->runMigration(new Migration1781000000ConsolidateContentSettings());

        $this->assertContentSettingsNull($slideId, $languageId);
        $this->assertLegacyTextColumnsDropped();
        $this->assertAllMediaColumnsDropped();
    }

    public function testTextOnlySchemaMigratesTitleWithoutMediaKeys(): void
    {
        $this->createSlideTable();
        $this->createAuthentic49TranslationTable();

        $slideId = $this->insertSlide([]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->insertTranslation($slideId, $languageId, [
            'name' => 'Text schema',
            'title' => 'Headline',
            'url' => '/x',
        ]);

        $this->runMigration(new Migration1781000000ConsolidateContentSettings());

        $decoded = $this->fetchContentSettings($slideId, $languageId);
        static::assertSame('Headline', $decoded['title']);
        static::assertSame('/x', $decoded['url']);
        static::assertArrayNotHasKey('slideCover', $decoded);
        static::assertArrayNotHasKey('focusImageId', $decoded);
        $this->assertLegacyTextColumnsDropped();
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_id');
    }

    public function testMediaMigrationPreservesExistingJsonText(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createAuthentic49TranslationTable(true);

        $coverId = Uuid::randomBytes();
        $slideId = $this->insertSlide(['slide_cover_id' => $coverId]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->insertTranslation($slideId, $languageId, [
            'name' => 'JSON text',
            'content_settings' => json_encode([
                'title' => 'Keep title',
                'description' => 'Keep body',
            ], \JSON_THROW_ON_ERROR),
        ]);

        $this->runMigration(new Migration1781000000ConsolidateContentSettings());

        $decoded = $this->fetchContentSettings($slideId, $languageId);
        static::assertSame('Keep title', $decoded['title']);
        static::assertSame('Keep body', $decoded['description']);
        static::assertSame(Uuid::fromBytesToHex($coverId), $decoded['slideCover']['desktopId']);
    }

    public function testMigrationHandlesMixedBatchOfSlides(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createAuthentic49TranslationTable();
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);

        $emptyId = $this->insertSlide([]);
        $this->insertTranslation($emptyId, $languageId, ['name' => 'Empty']);

        $textId = $this->insertSlide([]);
        $this->insertTranslation($textId, $languageId, [
            'name' => 'Text',
            'title' => 'Text only',
        ]);

        $coverId = Uuid::randomBytes();
        $mediaId = $this->insertSlide(['slide_cover_id' => $coverId]);
        $this->insertTranslation($mediaId, $languageId, ['name' => 'Media']);

        $fullCover = Uuid::randomBytes();
        $fullId = $this->insertSlide(['slide_cover_id' => $fullCover]);
        $this->insertTranslation($fullId, $languageId, [
            'name' => 'Full',
            'title' => 'Full title',
            'url' => '/full',
        ]);

        $this->runMigration(new Migration1781000000ConsolidateContentSettings());

        $this->assertContentSettingsNull($emptyId, $languageId);

        $text = $this->fetchContentSettings($textId, $languageId);
        static::assertSame('Text only', $text['title']);
        static::assertArrayNotHasKey('slideCover', $text);

        $media = $this->fetchContentSettings($mediaId, $languageId);
        static::assertSame(Uuid::fromBytesToHex($coverId), $media['slideCover']['desktopId']);
        static::assertArrayNotHasKey('title', $media);

        $full = $this->fetchContentSettings($fullId, $languageId);
        static::assertSame('Full title', $full['title']);
        static::assertSame('/full', $full['url']);
        static::assertSame(Uuid::fromBytesToHex($fullCover), $full['slideCover']['desktopId']);

        $this->assertLegacyTextColumnsDropped();
        $this->assertAllMediaColumnsDropped();
    }

    public function testMigrationMigratesMoreThanOneChunkOfSlides(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createAuthentic49TranslationTable();

        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $expected = [];

        for ($i = 0; $i < 101; ++$i) {
            $coverId = Uuid::randomBytes();
            $slideId = $this->insertSlide(['slide_cover_id' => $coverId]);
            $title = sprintf('Chunk title %03d', $i);
            $this->insertTranslation($slideId, $languageId, [
                'name' => sprintf('Chunk %03d', $i),
                'title' => $title,
            ]);
            $expected[Uuid::fromBytesToHex($slideId)] = [
                'title' => $title,
                'cover' => Uuid::fromBytesToHex($coverId),
            ];
        }

        $this->runMigration(new Migration1781000000ConsolidateContentSettings());

        $rows = $this->connection->fetchAllAssociative(
            'SELECT blur_elysium_slides_id, content_settings FROM blur_elysium_slides_translation'
        );
        static::assertCount(101, $rows);

        foreach ($rows as $row) {
            $hex = Uuid::fromBytesToHex($row['blur_elysium_slides_id']);
            static::assertArrayHasKey($hex, $expected);
            $decoded = json_decode((string) $row['content_settings'], true);
            static::assertIsArray($decoded);
            static::assertSame($expected[$hex]['title'], $decoded['title']);
            static::assertSame($expected[$hex]['cover'], $decoded['slideCover']['desktopId']);
        }

        $this->assertLegacyTextColumnsDropped();
        $this->assertAllMediaColumnsDropped();
    }

    /**
     * @return iterable<string, array{0: bool, 1: string}>
     */
    public static function nonObjectContentSettingsCases(): iterable
    {
        yield 'invalid json' => [true, '{broken'];
        yield 'json list' => [false, '[1,2,3]'];
        yield 'json number' => [false, '42'];
    }

    #[DataProvider('nonObjectContentSettingsCases')]
    public function testNonObjectContentSettingsAbortsWithoutDroppingColumns(bool $looseJson, string $rawJson): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createAuthentic49TranslationTable(true, $looseJson);

        $slideId = $this->insertSlide([]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->insertTranslation($slideId, $languageId, [
            'name' => 'Corrupt JSON',
            'title' => 'Should not copy',
            'content_settings' => $rawJson,
        ]);

        try {
            $this->runMigration(new Migration1781000000ConsolidateContentSettings());
            static::fail('Expected non-object content_settings to abort the migration');
        } catch (\RuntimeException $e) {
            static::assertStringContainsString('non-object content_settings', $e->getMessage());
        }

        $this->assertColumnExists('blur_elysium_slides_translation', 'title');
        $this->assertColumnExists('blur_elysium_slides', 'slide_cover_id');
        $title = $this->connection->fetchOne(
            'SELECT title FROM blur_elysium_slides_translation WHERE blur_elysium_slides_id = ? AND language_id = ?',
            [$slideId, $languageId]
        );
        static::assertSame('Should not copy', $title);
    }

    public function testVerifyFailureDoesNotDropTextColumns(): void
    {
        $this->createSlideTable();
        $this->createAuthentic49TranslationTable();

        $slideId = $this->insertSlide([]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->insertTranslation($slideId, $languageId, [
            'name' => 'Unmigrated text',
            'title' => 'Still here',
        ]);

        $migration = new class extends Migration1781000000ConsolidateContentSettings {
            protected function migrateTranslationData(Connection $connection): void
            {
            }
        };

        try {
            $this->runMigration($migration);
            static::fail('Expected contentSettings verification to fail');
        } catch (\RuntimeException $e) {
            static::assertStringContainsString('verification failed', $e->getMessage());
        }

        $this->assertColumnExists('blur_elysium_slides_translation', 'title');
    }

    public function testMigrationDropsMediaForeignKeysAndIndexesButKeepsProductAndCategory(): void
    {
        $this->createSlideTableWithProductCategoryAndAllMediaForeignKeys();
        $this->createAuthentic49TranslationTable();

        $this->runMigration(new Migration1781000000ConsolidateContentSettings());

        $this->assertAllMediaColumnsDropped();
        $this->assertForeignKeyExists('blur_elysium_slides', 'fk.blur_elysium_slides.product_id');
        $this->assertForeignKeyExists('blur_elysium_slides', 'fk.blur_elysium_slides.category_id');
        $this->assertForeignKeyDoesNotExist('blur_elysium_slides', 'fk.blur_elysium_slides.slide_cover_id');
        $this->assertForeignKeyDoesNotExist('blur_elysium_slides', 'fk.blur_elysium_slides.slide_cover_mobile_id');
        $this->assertForeignKeyDoesNotExist('blur_elysium_slides', 'fk.blur_elysium_slides.slide_cover_tablet_id');
        $this->assertForeignKeyDoesNotExist('blur_elysium_slides', 'fk.blur_elysium_slides.slide_cover_video_id');
        $this->assertForeignKeyDoesNotExist('blur_elysium_slides', 'fk.blur_elysium_slides.presentation_media_id');

        $mediaIndexColumns = $this->connection->fetchFirstColumn(
            "SELECT COLUMN_NAME FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = 'blur_elysium_slides'
             AND COLUMN_NAME IN (
                'slide_cover_id',
                'slide_cover_mobile_id',
                'slide_cover_tablet_id',
                'slide_cover_video_id',
                'presentation_media_id'
             )"
        );
        static::assertSame([], $mediaIndexColumns);
    }

    public function testMigrationIsIdempotentOnAuthentic49Schema(): void
    {
        $this->createSlideTableWithAllMediaColumns();
        $this->createAuthentic49TranslationTable();

        $slideId = $this->insertSlide([
            'slide_cover_id' => Uuid::randomBytes(),
        ]);
        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->insertTranslation($slideId, $languageId, [
            'name' => 'Idempotent',
            'title' => 'Once',
        ]);

        $migration = new Migration1781000000ConsolidateContentSettings();
        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertLegacyTextColumnsDropped();
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_id');
        $this->assertColumnExists('blur_elysium_slides_translation', 'content_settings');
        static::assertSame('Once', $this->fetchContentSettings($slideId, $languageId)['title']);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function insertTranslation(string $slideId, string $languageId, array $data): void
    {
        $data['blur_elysium_slides_id'] = $slideId;
        $data['language_id'] = $languageId;
        $data['created_at'] = $data['created_at'] ?? (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        $this->connection->insert('blur_elysium_slides_translation', $data);
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchContentSettings(string $slideId, string $languageId): array
    {
        $contentSettings = $this->connection->fetchOne(
            'SELECT content_settings FROM blur_elysium_slides_translation WHERE blur_elysium_slides_id = ? AND language_id = ?',
            [$slideId, $languageId]
        );

        static::assertNotFalse($contentSettings);
        static::assertNotNull($contentSettings);
        $decoded = json_decode((string) $contentSettings, true);
        static::assertIsArray($decoded);

        return $decoded;
    }

    private function assertContentSettingsNull(string $slideId, string $languageId): void
    {
        $contentSettings = $this->connection->fetchOne(
            'SELECT content_settings FROM blur_elysium_slides_translation WHERE blur_elysium_slides_id = ? AND language_id = ?',
            [$slideId, $languageId]
        );

        static::assertNotFalse($contentSettings, 'Translation row should exist');
        static::assertTrue(
            $contentSettings === null || $contentSettings === '',
            'content_settings should stay empty when there is nothing to copy'
        );
    }

    /**
     * @param array<string, mixed> $expected
     * @param array<string, mixed> $actual
     */
    private function assertContentSettingsMatch(array $expected, array $actual): void
    {
        static::assertEquals($expected, $actual);
    }

    /**
     * @param array<string, string> $tokens
     */
    private function interpolate(mixed $value, array $tokens): mixed
    {
        if (\is_string($value)) {
            return strtr($value, $tokens);
        }

        if (!\is_array($value)) {
            return $value;
        }

        $interpolated = [];
        foreach ($value as $key => $item) {
            $interpolated[$key] = $this->interpolate($item, $tokens);
        }

        return $interpolated;
    }

    /**
     * @return array{hasText: bool, mediaColumns: list<string>}
     */
    private function setUpSmokeSchema(string $schema): array
    {
        $allMedia = array_values(self::MEDIA_KEYS);

        return match ($schema) {
            'authentic49' => $this->bootAuthentic49(false, false, $allMedia),
            'authentic49Json' => $this->bootAuthentic49(true, false, $allMedia),
            'authentic49JsonLoose' => $this->bootAuthentic49(true, true, $allMedia),
            default => throw new \InvalidArgumentException('Unknown smoke schema: ' . $schema),
        };
    }

    /**
     * @param list<string> $mediaColumns
     * @return array{hasText: bool, mediaColumns: list<string>}
     */
    private function bootAuthentic49(bool $withContentSettings, bool $looseJson, array $mediaColumns): array
    {
        $this->createSlideTable(array_map(
            static fn (string $column): string => '`' . $column . '` BINARY(16) NULL',
            $mediaColumns
        ));
        $this->createAuthentic49TranslationTable($withContentSettings, $looseJson);

        return ['hasText' => true, 'mediaColumns' => $mediaColumns];
    }

    private function createAuthentic49TranslationTable(bool $withContentSettings = false, bool $looseJson = false): void
    {
        $this->dropTableIfExists('blur_elysium_slides_translation');

        $contentSettingsColumn = '';
        if ($withContentSettings) {
            $type = $looseJson ? 'LONGTEXT' : 'JSON';
            $contentSettingsColumn = "`content_settings` {$type} NULL,";
        }

        $this->connection->executeStatement("
            CREATE TABLE `blur_elysium_slides_translation` (
                `blur_elysium_slides_id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `title` VARCHAR(255) NULL,
                `description` LONGTEXT NULL,
                `button_label` VARCHAR(255) NULL,
                `url` LONGTEXT NULL,
                {$contentSettingsColumn}
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`blur_elysium_slides_id`, `language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    /**
     * @return array<string, mixed>
     */
    private static function mediaExpected(string $mediaKey, string $token = ''): array
    {
        $token = $token !== '' ? $token : '%' . $mediaKey . '%';

        return match ($mediaKey) {
            'desktop' => ['slideCover' => ['desktopId' => $token]],
            'mobile' => ['slideCover' => ['mobileId' => $token]],
            'tablet' => ['slideCover' => ['tabletId' => $token]],
            'video' => ['slideCover' => ['videoId' => $token]],
            'focus' => ['focusImageId' => $token],
            default => throw new \InvalidArgumentException('Unknown media key: ' . $mediaKey),
        };
    }

    private function assertLegacyTextColumnsDropped(): void
    {
        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'title');
        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'description');
        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'button_label');
        $this->assertColumnDoesNotExist('blur_elysium_slides_translation', 'url');
    }

    private function createSlideTableWithProductCategoryAndAllMediaForeignKeys(): void
    {
        $this->dropTableIfExists('blur_elysium_slides_translation');
        $this->dropTableIfExists('blur_elysium_slides');

        $this->connection->executeStatement("
            CREATE TABLE `blur_elysium_slides` (
                `id` BINARY(16) NOT NULL,
                `product_id` BINARY(16) NULL,
                `product_version_id` BINARY(16) NULL,
                `category_id` BINARY(16) NULL,
                `category_version_id` BINARY(16) NULL,
                `slide_cover_id` BINARY(16) NULL,
                `slide_cover_mobile_id` BINARY(16) NULL,
                `slide_cover_tablet_id` BINARY(16) NULL,
                `slide_cover_video_id` BINARY(16) NULL,
                `presentation_media_id` BINARY(16) NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk.blur_elysium_slides.product_id` FOREIGN KEY (`product_id`, `product_version_id`)
                    REFERENCES `product` (`id`, `version_id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk.blur_elysium_slides.category_id` FOREIGN KEY (`category_id`, `category_version_id`)
                    REFERENCES `category` (`id`, `version_id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk.blur_elysium_slides.slide_cover_id` FOREIGN KEY (`slide_cover_id`)
                    REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk.blur_elysium_slides.slide_cover_mobile_id` FOREIGN KEY (`slide_cover_mobile_id`)
                    REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk.blur_elysium_slides.slide_cover_tablet_id` FOREIGN KEY (`slide_cover_tablet_id`)
                    REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk.blur_elysium_slides.slide_cover_video_id` FOREIGN KEY (`slide_cover_video_id`)
                    REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk.blur_elysium_slides.presentation_media_id` FOREIGN KEY (`presentation_media_id`)
                    REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private function createSlideTableWithAllMediaColumns(): void
    {
        $this->createSlideTable([
            '`slide_cover_id` BINARY(16) NULL',
            '`slide_cover_mobile_id` BINARY(16) NULL',
            '`slide_cover_tablet_id` BINARY(16) NULL',
            '`slide_cover_video_id` BINARY(16) NULL',
            '`presentation_media_id` BINARY(16) NULL',
        ]);
    }

    private function assertAllMediaColumnsDropped(): void
    {
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_id');
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_mobile_id');
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_tablet_id');
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'slide_cover_video_id');
        $this->assertColumnDoesNotExist('blur_elysium_slides', 'presentation_media_id');
    }

    protected function assertColumnDoesNotExist(string $table, string $column, string $message = ''): void
    {
        $exists = $this->connection->fetchOne(
            'SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            [$table, $column]
        );

        static::assertFalse($exists, $message ?: "Column '{$column}' should NOT exist in table '{$table}'");
    }

    protected function assertForeignKeyDoesNotExist(string $table, string $fkName, string $message = ''): void
    {
        $exists = $this->connection->fetchOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = \'FOREIGN KEY\' AND TABLE_SCHEMA = DATABASE()',
            [$table, $fkName]
        );

        static::assertFalse($exists, $message ?: "Foreign key '{$fkName}' should NOT exist in table '{$table}'");
    }

    private function createSlideTableWithProductCategoryAndMediaForeignKeys(): void
    {
        $this->dropTableIfExists('blur_elysium_slides_translation');
        $this->dropTableIfExists('blur_elysium_slides');

        $this->connection->executeStatement("
            CREATE TABLE `blur_elysium_slides` (
                `id` BINARY(16) NOT NULL,
                `product_id` BINARY(16) NULL,
                `product_version_id` BINARY(16) NULL,
                `category_id` BINARY(16) NULL,
                `category_version_id` BINARY(16) NULL,
                `slide_cover_id` BINARY(16) NULL,
                `slide_cover_mobile_id` BINARY(16) NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk.blur_elysium_slides.product_id` FOREIGN KEY (`product_id`, `product_version_id`)
                    REFERENCES `product` (`id`, `version_id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk.blur_elysium_slides.category_id` FOREIGN KEY (`category_id`, `category_version_id`)
                    REFERENCES `category` (`id`, `version_id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk.blur_elysium_slides.slide_cover_id` FOREIGN KEY (`slide_cover_id`)
                    REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk.blur_elysium_slides.slide_cover_mobile_id` FOREIGN KEY (`slide_cover_mobile_id`)
                    REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private function createFreshInstallTranslationTable(): void
    {
        $this->dropTableIfExists('blur_elysium_slides_translation');

        $this->connection->executeStatement("
            CREATE TABLE `blur_elysium_slides_translation` (
                `blur_elysium_slides_id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `content_settings` JSON NULL,
                `custom_fields` JSON NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`blur_elysium_slides_id`, `language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private function createUpgradeTranslationTable(): void
    {
        $this->dropTableIfExists('blur_elysium_slides_translation');

        $this->connection->executeStatement("
            CREATE TABLE `blur_elysium_slides_translation` (
                `blur_elysium_slides_id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `title` VARCHAR(255) NULL,
                `description` TEXT NULL,
                `button_label` VARCHAR(255) NULL,
                `url` VARCHAR(255) NULL,
                `content_settings` JSON NULL,
                `custom_fields` JSON NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`blur_elysium_slides_id`, `language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
}
