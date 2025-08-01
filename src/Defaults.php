<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider;

final class Defaults
{
    public const MIGRATION_COLUMN_EXISTS = '/duplicate column|column exists|duplicate key/i';

    public const MIGRATION_COLUMN_NOT_EXISTS = '/unknown column|SQLSTATE[42S22]/i';

    /**
     * @var string
     */
    public const MEDIA_FOLDER_NAME = 'Slide Builder';

    /**
     * @var string
     */
    public const MEDIA_FOLDER_ID = '474bbe09861b632e5d742b434e62eceb';

    /**
     * @var array
     */
    public const MEDIA_THUMBNAIL_SIZES = [
        ['width' => 400, 'height' => 400],
        ['width' => 800, 'height' => 800],
        ['width' => 1920, 'height' => 1920],
    ];

    public const CMS_SECTION_NAME = 'blur-elysium-section';

    public const CMS_SECTION_SETTINGS_KEY = 'elysiumSectionSettings';

    /**
     * Provides default section configuration settings for the Elysium Section.
     *
     * @return array<string, mixed[]>
     */
    public static function cmsSectionSettings(): array
    {
        return [
            self::CMS_SECTION_SETTINGS_KEY => [
                'breakpoints' => [
                    'mobile' => null,
                    'tablet' => null,
                    'desktop' => null,
                ],
                'viewports' => [
                    'mobile' => [
                        'gridCols' => 12,
                        'gridGap' => 20,
                        'alignItems' => 'stretch',
                        'paddingY' => 20,
                        'paddingX' => 0,
                    ],
                    'tablet' => [
                        'gridCols' => 12,
                        'gridGap' => 40,
                        'alignItems' => 'stretch',
                        'paddingY' => 40,
                        'paddingX' => 0,
                    ],
                    'desktop' => [
                        'gridCols' => 12,
                        'gridGap' => 40,
                        'alignItems' => 'stretch',
                        'paddingY' => 40,
                        'paddingX' => 0,
                    ],
                ],
            ],
        ];
    }
}
