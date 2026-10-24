<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CreateSrcset extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('create_srcset', [$this, 'createSrcset']),
            new TwigFunction('srcset_url', [$this, 'encodeSrcsetUrl']),
        ];
    }

    /**
     * Encode a URL for use in a srcset attribute.
     *
     * srcset splits candidates on commas and the URL from descriptors on
     * whitespace, so a file name like "Hintergrund Startseite.png" would
     * otherwise be parsed as an invalid descriptor and the source skipped.
     */
    public function encodeSrcsetUrl(mixed $url): string
    {
        if (!\is_string($url) || $url === '') {
            return '';
        }

        return str_replace(
            [' ', ',', "\t", "\n", "\r"],
            ['%20', '%2C', '%09', '%0A', '%0D'],
            $url
        );
    }

    public function createSrcset(mixed $thumbnails, mixed $originalUrl = null, mixed $originalWidth = null): string
    {
        $candidates = [];
        $seen = [];

        if (is_iterable($thumbnails)) {
            foreach ($thumbnails as $thumbnail) {
                if (\is_array($thumbnail)) {
                    $thumbWidth = (int) ($thumbnail['width'] ?? 0);
                    $thumbUrl = $thumbnail['url'] ?? '';
                } else {
                    $thumbWidth = (int) $thumbnail->getWidth();
                    $thumbUrl = $thumbnail->getUrl();
                }

                if ($thumbWidth <= 0) {
                    continue;
                }

                $this->pushCandidate($candidates, $seen, $thumbUrl, $thumbWidth);
            }
        }

        if (\is_string($originalUrl) && $originalUrl !== '') {
            $width = is_numeric($originalWidth) ? (int) $originalWidth : 0;
            if ($width > 0) {
                $this->pushCandidate($candidates, $seen, $originalUrl, $width);
            } elseif ($candidates === []) {
                $this->pushCandidate($candidates, $seen, $originalUrl, 0);
            }
        }

        return implode(',', $candidates);
    }

    /**
     * @param list<string> $candidates
     * @param array<string, true> $seen
     */
    private function pushCandidate(array &$candidates, array &$seen, mixed $url, int $width): void
    {
        $encoded = $this->encodeSrcsetUrl(\is_string($url) ? $url : '');
        if ($encoded === '') {
            return;
        }

        $entry = $width > 0 ? $encoded . ' ' . $width . 'w' : $encoded;
        if (isset($seen[$entry])) {
            return;
        }

        $seen[$entry] = true;
        $candidates[] = $entry;
    }
}
