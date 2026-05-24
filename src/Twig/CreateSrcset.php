<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CreateSrcset extends AbstractExtension
{
    public function getFunctions(): mixed
    {
        return [
            new TwigFunction('create_srcset', [$this, 'createSrcset']),
        ];
    }

    public function createSrcset(mixed $thumbnails): string
    {
        $srcset = [];
        foreach ($thumbnails as $thumbnail) {
            if (is_array($thumbnail)) {
                $thumbWidth = (string) ($thumbnail['width'] ?? 0);
                $thumbUrl = $thumbnail['url'] ?? '';
            } else {
                $thumbWidth = (string) $thumbnail->getWidth();
                $thumbUrl = $thumbnail->getUrl();
            }
            $srcset[] = "{$thumbUrl} {$thumbWidth}w";
        }

        return implode(',', $srcset);
    }
}
