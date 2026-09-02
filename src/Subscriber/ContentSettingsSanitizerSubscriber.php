<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Subscriber;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation\ElysiumSlidesTranslationDefinition;
use Shopware\Core\Framework\Api\Acl\Role\AclRoleDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWriteEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommand;
use Shopware\Core\Framework\Util\HtmlSanitizer;
use Shopware\Core\Framework\Util\Json;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentSettingsSanitizerSubscriber implements EventSubscriberInterface
{
    public const TITLE_FIELD = 'blur_elysium_slides_translation.contentSettings.title';

    public const DESCRIPTION_FIELD = 'blur_elysium_slides_translation.contentSettings.description';

    public function __construct(
        private readonly HtmlSanitizer $htmlSanitizer
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EntityWriteEvent::class => 'sanitizeContentSettings',
        ];
    }

    public function sanitizeContentSettings(EntityWriteEvent $event): void
    {
        foreach ($event->getCommandsForEntity(ElysiumSlidesTranslationDefinition::ENTITY_NAME) as $command) {
            if ($command->getPrivilege() === AclRoleDefinition::PRIVILEGE_DELETE) {
                continue;
            }

            $this->sanitizeCommand($command);
        }
    }

    private function sanitizeCommand(WriteCommand $command): void
    {
        if (!$command->hasField('content_settings')) {
            return;
        }

        $raw = $command->getPayload()['content_settings'];
        $contentSettings = $this->decodeContentSettings($raw);

        if ($contentSettings === null) {
            return;
        }

        $changed = false;

        if (isset($contentSettings['title']) && \is_string($contentSettings['title'])) {
            $contentSettings['title'] = $this->htmlSanitizer->sanitize(
                $contentSettings['title'],
                [],
                false,
                self::TITLE_FIELD
            );
            $changed = true;
        }

        if (isset($contentSettings['description']) && \is_string($contentSettings['description'])) {
            $contentSettings['description'] = $this->htmlSanitizer->sanitize(
                $contentSettings['description'],
                [],
                false,
                self::DESCRIPTION_FIELD
            );
            $changed = true;
        }

        if (!$changed) {
            return;
        }

        $command->addPayload('content_settings', Json::encode($contentSettings));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeContentSettings(mixed $raw): ?array
    {
        if (\is_array($raw)) {
            return $raw;
        }

        if (!\is_string($raw) || $raw === '') {
            return null;
        }

        try {
            $decoded = json_decode($raw, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        return \is_array($decoded) ? $decoded : null;
    }
}
