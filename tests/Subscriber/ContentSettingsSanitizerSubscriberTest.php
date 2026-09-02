<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Subscriber;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation\ElysiumSlidesTranslationDefinition;
use Blur\BlurElysiumSlider\Subscriber\ContentSettingsSanitizerSubscriber;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Api\Acl\Role\AclRoleDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWriteEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Shopware\Core\Framework\Util\HtmlSanitizer;
use Shopware\Core\Framework\Uuid\Uuid;

class ContentSettingsSanitizerSubscriberTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $events = ContentSettingsSanitizerSubscriber::getSubscribedEvents();

        static::assertArrayHasKey(EntityWriteEvent::class, $events);
        static::assertSame('sanitizeContentSettings', $events[EntityWriteEvent::class]);
    }

    public function testSanitizesTitleAndDescriptionOnWrite(): void
    {
        $sanitizer = $this->createMock(HtmlSanitizer::class);
        $sanitizer->expects($this->exactly(2))
            ->method('sanitize')
            ->willReturnCallback(function (string $text, array $options, bool $override, ?string $field): string {
                static::assertSame([], $options);
                static::assertFalse($override);

                if ($field === ContentSettingsSanitizerSubscriber::TITLE_FIELD) {
                    static::assertSame('<script>x</script>Hello', $text);

                    return 'Hello';
                }

                static::assertSame(ContentSettingsSanitizerSubscriber::DESCRIPTION_FIELD, $field);
                static::assertSame('<img onerror="alert(1)" src="x">Text', $text);

                return '<img src="x">Text';
            });

        $command = $this->createCommand('create', [
            'content_settings' => json_encode([
                'title' => '<script>x</script>Hello',
                'description' => '<img onerror="alert(1)" src="x">Text',
                'url' => '/safe',
            ], \JSON_THROW_ON_ERROR),
        ]);

        $subscriber = new ContentSettingsSanitizerSubscriber($sanitizer);
        $subscriber->sanitizeContentSettings($this->createEvent([$command]));

        $payload = $command->getPayload();
        $decoded = json_decode($payload['content_settings'], true, 512, \JSON_THROW_ON_ERROR);

        static::assertSame('Hello', $decoded['title']);
        static::assertSame('<img src="x">Text', $decoded['description']);
        static::assertSame('/safe', $decoded['url']);
    }

    public function testSkipsDeleteCommands(): void
    {
        $sanitizer = $this->createMock(HtmlSanitizer::class);
        $sanitizer->expects($this->never())->method('sanitize');

        $command = $this->createCommand(AclRoleDefinition::PRIVILEGE_DELETE, [
            'content_settings' => json_encode(['title' => '<script>x</script>'], \JSON_THROW_ON_ERROR),
        ]);

        $subscriber = new ContentSettingsSanitizerSubscriber($sanitizer);
        $subscriber->sanitizeContentSettings($this->createEvent([$command]));
    }

    public function testSkipsCommandsWithoutContentSettings(): void
    {
        $sanitizer = $this->createMock(HtmlSanitizer::class);
        $sanitizer->expects($this->never())->method('sanitize');

        $command = $this->createCommand('update', ['name' => 'Slide']);

        $subscriber = new ContentSettingsSanitizerSubscriber($sanitizer);
        $subscriber->sanitizeContentSettings($this->createEvent([$command]));
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function createCommand(string $privilege, array $payload): WriteCommand
    {
        return new SanitizerTestWriteCommand($privilege, $payload);
    }

    /**
     * @param list<WriteCommand> $commands
     */
    private function createEvent(array $commands): EntityWriteEvent
    {
        return EntityWriteEvent::create(
            WriteContext::createFromContext(Context::createDefaultContext()),
            $commands
        );
    }
}

class SanitizerTestWriteCommand extends WriteCommand
{
    private readonly string $privilege;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(string $privilege, array $payload)
    {
        $this->privilege = $privilege;
        $this->payload = $payload;
        $this->primaryKey = [
            'blur_elysium_slides_id' => Uuid::randomBytes(),
            'language_id' => Uuid::randomBytes(),
        ];
        $this->entityName = ElysiumSlidesTranslationDefinition::ENTITY_NAME;
        $this->existence = new EntityExistence(ElysiumSlidesTranslationDefinition::ENTITY_NAME, [], false, false, false, []);
        $this->path = '/';
    }

    public function getPrivilege(): string
    {
        return $this->privilege;
    }

    public function addPayload(string $key, mixed $value): void
    {
        $this->payload[$key] = $value;
    }
}
