<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Demodata;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Demodata\ElysiumSlidesGenerator;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Defaults;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Shopware\Core\Content\Media\File\FileNameProvider;
use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Write\EntityWriterInterface;
use Shopware\Core\Framework\Demodata\DemodataContext;
use Shopware\Core\Framework\Demodata\DemodataGeneratorInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class ElysiumSlidesGeneratorTest extends TestCase
{
    /**
     * Mirrors SlideValidationSubscriber::NAME_PATTERN exactly.
     * If this regex changes, the slide name validation must be updated too.
     */
    private const NAME_PATTERN = '/^[A-Za-z0-9][A-Za-z0-9\s-]*[A-Za-z0-9]$|^[A-Za-z0-9]$/';

    public function testImplementsDemodataGeneratorInterface(): void
    {
        static::assertContains(
            DemodataGeneratorInterface::class,
            class_implements(ElysiumSlidesGenerator::class)
        );
    }

    public function testGetDefinitionReturnsElysiumSlidesDefinition(): void
    {
        $generator = $this->createGenerator();

        static::assertSame(ElysiumSlidesDefinition::class, $generator->getDefinition());
    }

    public function testClassIsMarkedInternal(): void
    {
        $reflection = new ReflectionClass(ElysiumSlidesGenerator::class);
        $docComment = $reflection->getDocComment() ?: '';

        static::assertStringContainsString('@internal', $docComment);
    }

    public function testConstructorParameters(): void
    {
        $reflection = new ReflectionClass(ElysiumSlidesGenerator::class);
        $params = $reflection->getConstructor()->getParameters();

        static::assertCount(7, $params);
        static::assertSame('writer', $params[0]->getName());
        static::assertSame('fileSaver', $params[1]->getName());
        static::assertSame('fileNameProvider', $params[2]->getName());
        static::assertSame('elysiumSlidesDefinition', $params[3]->getName());
        static::assertSame('mediaDefinition', $params[4]->getName());
        static::assertSame('connection', $params[5]->getName());
        static::assertSame('projectDir', $params[6]->getName());
    }

    public function testEnsureDependenciesThrowsWhenImagesGeneratorMissing(): void
    {
        if (class_exists(\Maltyxx\ImagesGenerator\ImagesGeneratorProvider::class)) {
            static::markTestSkipped('Maltyxx/ImagesGenerator is installed; cannot test the missing-dependency path.');
        }

        $generator = $this->createGenerator();
        $reflection = new ReflectionClass($generator);
        $method = $reflection->getMethod('ensureDependencies');
        $method->setAccessible(true);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/shopware\/dev-tools/');

        $method->invoke($generator);
    }

    public function testEnsureDependenciesDoesNotThrowWhenImagesGeneratorPresent(): void
    {
        if (!class_exists(\Maltyxx\ImagesGenerator\ImagesGeneratorProvider::class)) {
            static::markTestSkipped('Maltyxx/ImagesGenerator is not installed; cannot test the present-dependency path.');
        }

        $generator = $this->createGenerator();
        $reflection = new ReflectionClass($generator);
        $method = $reflection->getMethod('ensureDependencies');
        $method->setAccessible(true);

        $method->invoke($generator);

        static::assertTrue(true);
    }

    public function testSlideNameMatchesValidationPattern(): void
    {
        $generator = $this->createGenerator();
        $context = $this->createDemodataContext();

        for ($i = 1; $i <= 5; ++$i) {
            $payload = $generator->assembleSlidePayload(
                $context,
                $i,
                str_repeat('a', 32),
                str_repeat('b', 32),
            );

            static::assertArrayHasKey('name', $payload);
            static::assertMatchesRegularExpression(self::NAME_PATTERN, $payload['name'], sprintf('Slide name "%s" must match NAME_PATTERN', $payload['name']));
        }
    }

    public function testTitleIsPlainTextWithoutHtmlTags(): void
    {
        $generator = $this->createGenerator();
        $context = $this->createDemodataContext();

        for ($i = 1; $i <= 10; ++$i) {
            $payload = $generator->assembleSlidePayload(
                $context,
                $i,
                str_repeat('a', 32),
                str_repeat('b', 32),
            );

            static::assertArrayHasKey('title', $payload);
            static::assertDoesNotMatchRegularExpression('/<\\/?[a-z][^>]*>/i', $payload['title'], sprintf('Slide title "%s" must not contain HTML tags', $payload['title']));
        }
    }

    public function testSlideSettingsHeadlineHasSupportedElement(): void
    {
        $generator = $this->createGenerator();
        $context = $this->createDemodataContext();

        $payload = $generator->assembleSlidePayload(
            $context,
            1,
            str_repeat('a', 32),
            str_repeat('b', 32),
        );

        $supported = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div', 'span'];

        static::assertArrayHasKey('slideSettings', $payload);
        static::assertArrayHasKey('slide', $payload['slideSettings']);
        static::assertArrayHasKey('headline', $payload['slideSettings']['slide']);
        static::assertArrayHasKey('element', $payload['slideSettings']['slide']['headline']);
        static::assertContains($payload['slideSettings']['slide']['headline']['element'], $supported);
    }

    private function createGenerator(): ElysiumSlidesGenerator
    {
        return new ElysiumSlidesGenerator(
            $this->createStub(EntityWriterInterface::class),
            $this->createStub(FileSaver::class),
            $this->createStub(FileNameProvider::class),
            $this->createStub(ElysiumSlidesDefinition::class),
            $this->createStub(MediaDefinition::class),
            $this->createStub(Connection::class),
            '/tmp/elysium-test',
        );
    }

    private function createDemodataContext(): DemodataContext
    {
        $context = $this->createStub(DemodataContext::class);
        $faker = \Faker\Factory::create();
        $context->method('getFaker')->willReturn($faker);
        $context->method('getConsole')->willReturn(new SymfonyStyle(
            new ArrayInput([]),
            new BufferedOutput(),
        ));

        return $context;
    }
}

