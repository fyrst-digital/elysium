<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Command;

use Blur\BlurElysiumSlider\Command\SlideImportCommand;
use Blur\BlurElysiumSlider\Service\ImportExport\ImportResult;
use Blur\BlurElysiumSlider\Service\ImportExport\SlideImportService;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class SlideImportCommandTest extends TestCase
{
    public function testExecuteWithAbsolutePath(): void
    {
        $importService = $this->createMock(SlideImportService::class);
        $importService->expects($this->once())
            ->method('import')
            ->willReturn(new ImportResult(2, []));

        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->expects($this->never())
            ->method('fileExists');
        $filesystem->expects($this->never())
            ->method('read');

        $command = new SlideImportCommand($importService, $filesystem);

        $tempFile = tempnam(sys_get_temp_dir(), 'elysium-import-test');
        file_put_contents($tempFile, 'jsonl-content');

        $input = new ArrayInput([
            'file' => $tempFile,
        ]);
        $output = new BufferedOutput();

        $exitCode = $command->run($input, $output);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Imported 2 slide(s)', $output->fetch());

        unlink($tempFile);
    }

    public function testExecuteWithRelativePath(): void
    {
        $importService = $this->createMock(SlideImportService::class);
        $importService->expects($this->once())
            ->method('import')
            ->willReturn(new ImportResult(2, []));

        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->expects($this->once())
            ->method('fileExists')
            ->with('elysium/slides.jsonl')
            ->willReturn(true);
        $filesystem->expects($this->once())
            ->method('read')
            ->with('elysium/slides.jsonl')
            ->willReturn('jsonl-content');

        $command = new SlideImportCommand($importService, $filesystem);

        $input = new ArrayInput([
            'file' => 'slides.jsonl',
        ]);
        $output = new BufferedOutput();

        $exitCode = $command->run($input, $output);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Imported 2 slide(s)', $output->fetch());
    }

    public function testExecuteWithFileNotFound(): void
    {
        $importService = $this->createMock(SlideImportService::class);
        $importService->expects($this->never())->method('import');

        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->expects($this->once())
            ->method('fileExists')
            ->with('elysium/nonexistent.jsonl')
            ->willReturn(false);
        $filesystem->expects($this->never())
            ->method('read');

        $command = new SlideImportCommand($importService, $filesystem);

        $input = new ArrayInput([
            'file' => 'nonexistent.jsonl',
        ]);
        $output = new BufferedOutput();

        $exitCode = $command->run($input, $output);
        $outputContent = $output->fetch();

        $this->assertSame(2, $exitCode);
        $this->assertStringContainsString('File not found', $outputContent);
        $this->assertStringContainsString('nonexistent.jsonl', $outputContent);
        $this->assertStringContainsString('elysium/nonexistent.jsonl', $outputContent);
    }

    public function testExecuteWithErrors(): void
    {
        $importService = $this->createMock(SlideImportService::class);
        $importService->expects($this->once())
            ->method('import')
            ->willReturn(new ImportResult(1, ['Error: Invalid line']));

        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->expects($this->never())
            ->method('fileExists');
        $filesystem->expects($this->never())
            ->method('read');

        $command = new SlideImportCommand($importService, $filesystem);

        $tempFile = tempnam(sys_get_temp_dir(), 'elysium-import-test');
        file_put_contents($tempFile, 'jsonl-content');

        $input = new ArrayInput([
            'file' => $tempFile,
        ]);
        $output = new BufferedOutput();

        $exitCode = $command->run($input, $output);

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('Error: Invalid line', $output->fetch());

        unlink($tempFile);
    }
}
