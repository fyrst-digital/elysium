<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Subscriber;

use Blur\BlurElysiumSlider\Subscriber\UnusedMediaSubscriber;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Media\Event\UnusedMediaSearchEvent;
use Shopware\Core\Framework\Context;

class UnusedMediaSubscriberTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $events = UnusedMediaSubscriber::getSubscribedEvents();

        static::assertArrayHasKey(UnusedMediaSearchEvent::class, $events);
        static::assertSame('removeUsedMedia', $events[UnusedMediaSearchEvent::class]);
    }

    public function testMarksSlideMediaAsUsed(): void
    {
        $usedId = str_repeat('a', 32);
        $unusedId = str_repeat('b', 32);

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchFirstColumn')
            ->with(
                $this->stringContains('blur_elysium_slides_translation'),
                ['ids' => [$usedId, $unusedId]],
                ['ids' => ArrayParameterType::STRING]
            )
            ->willReturn([$usedId]);

        $event = new UnusedMediaSearchEvent([$usedId, $unusedId], Context::createDefaultContext());

        $subscriber = new UnusedMediaSubscriber($connection);
        $subscriber->removeUsedMedia($event);

        static::assertSame([$unusedId], $event->getUnusedIds());
    }

    public function testDoesNothingWhenThereAreNoCandidateIds(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->never())->method('fetchFirstColumn');

        $event = new UnusedMediaSearchEvent([], Context::createDefaultContext());

        $subscriber = new UnusedMediaSubscriber($connection);
        $subscriber->removeUsedMedia($event);

        static::assertSame([], $event->getUnusedIds());
    }
}
