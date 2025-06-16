<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Bootstrap\PostUpdate\Version210;

class SlideSettings
{
    /**
     * @var mixed[]
     */
    public array $slideSettings = [
        'slide' => [],
        'viewports' => [
            'mobile' => [
                'container' => [
                    'columnWrap' => true,
                ],
                'headline' => [
                    'fontSize' => 20,
                ],
                'description' => [
                    'fontSize' => 14,
                ],
                'button' => [
                    'size' => 'sm',
                ],
            ],
            'tablet' => [
                'headline' => [
                    'fontSize' => 24,
                ],
                'description' => [
                    'fontSize' => 16,
                ],
                'button' => [
                    'size' => 'default',
                ],
            ],
            'desktop' => [
                'headline' => [
                    'fontSize' => 32,
                ],
                'description' => [
                    'fontSize' => 20,
                ],
                'button' => [
                    'size' => 'lg',
                ],
            ],
        ],
    ];

    /**
     * @return mixed[]
     */
    public function getSettings(): array
    {
        foreach ($this->slideSettings['viewports'] as $viewport => $value) {
            $this->slideSettings['viewports'][$viewport] = $this->buildViewportSettings($value);
        }

        return $this->slideSettings;
    }

    /**
     * @param mixed[] $replace
     *
     * @return mixed[]
     */
    private function buildViewportSettings(?array $replace = null): array
    {
        $settings = [
            'container' => [
                'paddingX' => 15,
                'paddingY' => 15,
                'borderRadius' => 0,
                'maxWidth' => 0,
                'maxWidthDisabled' => true,
                'gap' => 20,
                'justifyContent' => 'normal',
                'alignItems' => 'center',
                'columnWrap' => false,
                'order' => 'default',
            ],
            'content' => [
                'paddingX' => 0,
                'paddingY' => 0,
                'maxWidth' => 0,
                'maxWidthDisabled' => true,
                'textAlign' => 'left',
            ],
            'image' => [
                'justifyContent' => 'center',
                'maxWidth' => 0,
                'maxWidthDisabled' => true,
            ],
            'slide' => [
                /** @var int */
                'paddingX' => 15,
                /** @var int */
                'paddingY' => 15,
                /** @var int */
                'borderRadius' => 0,
                /** @var 'stretch'|'flex-start'|'center'|'flex-end' */
                'alignItems' => 'center',
                /** @var 'flex-start'|'center'|'flex-end' */
                'justifyContent' => 'center',
            ],
            'coverImage' => [
                /** @var 'left'|'center'|'right' */
                'objectPosX' => 'center',
                /** @var 'top'|'center'|'bottom' */
                'objectPosY' => 'center',
                /** @var 'cover'|'contain'|'auto' */
                'objectFit' => 'cover',
            ],
            'coverVideo' => [
                /** @var 'left'|'center'|'right' */
                'objectPosX' => 'center',
                /** @var 'top'|'center'|'bottom' */
                'objectPosY' => 'center',
                /** @var 'cover'|'contain'|'auto' */
                'objectFit' => 'cover',
            ],
            'headline' => [
                'fontSize' => 20,
            ],
            'description' => [
                'fontSize' => 14,
            ],
            'button' => [
                'size' => 'default',
            ],
        ];

        if ($replace !== null && \count($replace) > 0) {
            $settings = array_replace_recursive($settings, $replace);
        }

        return $settings;
    }
}
