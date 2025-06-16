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
}
