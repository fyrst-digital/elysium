<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Command;

use Blur\BlurElysiumSlider\Command\SlideExportCommand;
use Blur\BlurElysiumSlider\Service\ImportExport\SlideExportService;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\TestCase;
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

        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->expects($this->once())
            ->method('directoryExists')
            ->with('elysium')
            ->willReturn(true);
        $filesystem->expects($this->once())
            ->method('write')
            ->with($this->matchesRegularExpression('/elysium\/elysium-slides-export-\d{4}-\d{2}-\d{2}-\d{6}\.jsonl/'), 'jsonl-content');

        $command = new SlideExportCommand($exportService, $filesystem);

        $input = new ArrayInput([
            '--ids' => 'id1,id2',
        ]);
        $output = new BufferedOutput();

        $exitCode = $command->run($input, $output);
        $outputContent = $output->fetch();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Exported slides to elysium/', $outputContent);
    }

    public function testExecuteWithAllFlag(): void
    {
        $exportService = $this->createMock(SlideExportService::class);
        $exportService->expects($this->once())
            ->method('exportAll')
            ->willReturn('jsonl-content');

        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->expects($this->once())
            ->method('directoryExists')
            ->with('elysium')
            ->willReturn(true);
        $filesystem->expects($this->once())
            ->method('write')
            ->with($this->matchesRegularExpression('/elysium\/elysium-slides-export-\d{4}-\d{2}-\d{2}-\d{6}\.jsonl/'), 'jsonl-content');

        $command = new SlideExportCommand($exportService, $filesystem);

        $input = new ArrayInput([
            '--all' => true,
        ]);
        $output = new BufferedOutput();

        $exitCode = $command->run($input, $output);
        $outputContent = $output->fetch();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Exported slides to elysium/', $outputContent);
    }

    public function testExecuteWithOutputFile(): void
    {
        $exportService = $this->createMock(SlideExportService::class);
        $exportService->expects($this->once())
            ->method('exportAll')
            ->willReturn('jsonl-content');

        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->expects($this->once())
            ->method('write')
            ->with('/tmp/elysium-export-test.jsonl', 'jsonl-content');

        $command = new SlideExportCommand($exportService, $filesystem);

        $input = new ArrayInput([
            '--output' => '/tmp/elysium-export-test.jsonl',
        ]);
        $output = new BufferedOutput();

        $exitCode = $command->run($input, $output);
        $outputContent = $output->fetch();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('/tmp/elysium-export-test.jsonl', $outputContent);
    }

    public function testExecuteWithIdsAndAllReturnsError(): void
    {
        $exportService = $this->createMock(SlideExportService::class);
        $exportService->expects($this->never())
            ->method('export');
        $exportService->expects($this->never())
            ->method('exportAll');

        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->expects($this->never())->method('write');

        $command = new SlideExportCommand($exportService, $filesystem);

        $input = new ArrayInput([
            '--ids' => 'id1,id2',
            '--all' => true,
        ]);
        $output = new BufferedOutput();

        $exitCode = $command->run($input, $output);

        $this->assertSame(2, $exitCode);
        $this->assertStringContainsString('Cannot use both --ids and --all', $output->fetch());
    }

    public function testExecuteCreatesDirectoryIfNotExists(): void
    {
        $exportService = $this->createMock(SlideExportService::class);
        $exportService->expects($this->once())
            ->method('exportAll')
            ->willReturn('jsonl-content');

        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->expects($this->once())
            ->method('directoryExists')
            ->with('elysium')
            ->willReturn(false);
        $filesystem->expects($this->once())
            ->method('createDirectory')
            ->with('elysium');
        $filesystem->expects($this->once())
            ->method('write')
            ->with($this->matchesRegularExpression('/elysium\/elysium-slides-export-\d{4}-\d{2}-\d{2}-\d{6}\.jsonl/'), 'jsonl-content');

        $command = new SlideExportCommand($exportService, $filesystem);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $exitCode = $command->run($input, $output);
        $outputContent = $output->fetch();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Exported slides to elysium/', $outputContent);
    }
}
