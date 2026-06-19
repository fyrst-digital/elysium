<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Command;

use Blur\BlurElysiumSlider\Command\SlideCoverImageSwitchCommand;
use Blur\BlurElysiumSlider\Service\SlideCoverImageSwitchService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class SlideCoverImageSwitchCommandTest extends TestCase
{
    public function testExecuteOutputsAffectedCount(): void
    {
        $switchService = $this->createMock(SlideCoverImageSwitchService::class);
        $switchService->expects($this->once())
            ->method('switch')
            ->willReturn(5);

        $command = new SlideCoverImageSwitchCommand($switchService);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $exitCode = $command->run($input, $output);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Switched cover images for 5 slide(s).', $output->fetch());
    }

    public function testExecuteOutputsZeroAffectedCount(): void
    {
        $switchService = $this->createMock(SlideCoverImageSwitchService::class);
        $switchService->expects($this->once())
            ->method('switch')
            ->willReturn(0);

        $command = new SlideCoverImageSwitchCommand($switchService);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $exitCode = $command->run($input, $output);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Switched cover images for 0 slide(s).', $output->fetch());
    }
}
