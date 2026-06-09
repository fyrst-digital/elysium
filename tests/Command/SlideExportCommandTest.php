<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Command;

use Blur\BlurElysiumSlider\Command\SlideExportCommand;
use Blur\BlurElysiumSlider\Service\ImportExport\SlideExportService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Feature;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class SlideExportCommandTest extends TestCase
{
    public function testExecuteWithIds(): void
    {
        $exportService = $this->createMock(SlideExportService::class);
        $exportService->expects($this->once())
            ->method('export')
            ->with(['id1', 'id2'])
            ->willReturn('jsonl-content');

        $command = new SlideExportCommand($exportService);

        $input = new ArrayInput([
            '--ids' => 'id1,id2',
        ]);
        $output = new BufferedOutput();

        $command->run($input, $output);

        $this->assertStringContainsString('jsonl-content', $output->fetch());
    }

    public function testExecuteWithOutputFile(): void
    {
        $exportService = $this->createMock(SlideExportService::class);
        $exportService->expects($this->once())
            ->method('exportAll')
            ->willReturn('jsonl-content');

        $command = new SlideExportCommand($exportService);

        $tempFile = tempnam(sys_get_temp_dir(), 'elysium-export-test');

        $input = new ArrayInput([
            '--output' => $tempFile,
        ]);
        $output = new BufferedOutput();

        $command->run($input, $output);

        $this->assertStringContainsString($tempFile, $output->fetch());
        $this->assertSame('jsonl-content', file_get_contents($tempFile));

        unlink($tempFile);
    }
}
