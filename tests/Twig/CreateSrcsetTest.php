<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Twig;

use Blur\BlurElysiumSlider\Twig\CreateSrcset;
use PHPUnit\Framework\TestCase;

class CreateSrcsetTest extends TestCase
{
    private CreateSrcset $extension;

    protected function setUp(): void
    {
        $this->extension = new CreateSrcset();
    }

    public function testEncodeSrcsetUrlReplacesWhitespaceAndCommas(): void
    {
        $url = "https://example.com/media/Hintergrund Startseite.png?ts=1";

        static::assertSame(
            'https://example.com/media/Hintergrund%20Startseite.png?ts=1',
            $this->extension->encodeSrcsetUrl($url)
        );
        static::assertSame(
            'https://example.com/a%2Cb.jpg',
            $this->extension->encodeSrcsetUrl('https://example.com/a,b.jpg')
        );
        static::assertSame('', $this->extension->encodeSrcsetUrl(null));
    }

    public function testCreateSrcsetEncodesSpacesInThumbnailUrls(): void
    {
        $srcset = $this->extension->createSrcset([
            ['url' => 'https://cdn.example/Hintergrund Startseite.png', 'width' => 800],
            ['url' => 'https://cdn.example/Hintergrund Startseite.png', 'width' => 1600],
        ]);

        static::assertSame(
            'https://cdn.example/Hintergrund%20Startseite.png 800w,https://cdn.example/Hintergrund%20Startseite.png 1600w',
            $srcset
        );
    }

    public function testCreateSrcsetAppendsOriginalImageWhenWidthIsKnown(): void
    {
        $srcset = $this->extension->createSrcset(
            [
                ['url' => 'https://cdn.example/thumb-400.png', 'width' => 400],
            ],
            'https://cdn.example/Hintergrund Startseite.png',
            1920
        );

        static::assertSame(
            'https://cdn.example/thumb-400.png 400w,https://cdn.example/Hintergrund%20Startseite.png 1920w',
            $srcset
        );
    }

    public function testCreateSrcsetFallsBackToEncodedOriginalWithoutWidthDescriptors(): void
    {
        static::assertSame(
            'https://cdn.example/Hintergrund%20Startseite.png',
            $this->extension->createSrcset([], 'https://cdn.example/Hintergrund Startseite.png', null)
        );
    }

    public function testCreateSrcsetSkipsInvalidThumbnailsAndAcceptsThumbnailObjects(): void
    {
        $thumbnail = new class {
            public function getWidth(): int
            {
                return 1200;
            }

            public function getUrl(): string
            {
                return 'https://cdn.example/desktop.png';
            }
        };

        $srcset = $this->extension->createSrcset([
            ['url' => 'https://cdn.example/skip.png', 'width' => 0],
            $thumbnail,
        ]);

        static::assertSame('https://cdn.example/desktop.png 1200w', $srcset);
    }

    public function testSrcsetUrlTwigFunctionIsRegistered(): void
    {
        $names = array_map(static fn ($function) => $function->getName(), $this->extension->getFunctions());

        static::assertContains('create_srcset', $names);
        static::assertContains('srcset_url', $names);
    }
}
