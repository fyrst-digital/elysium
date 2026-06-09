<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Command;

use Blur\BlurElysiumSlider\Command\SlideImportCommand;
use Blur\BlurElysiumSlider\Service\ImportExport\ImportResult;
use Blur\BlurElysiumSlider\Service\ImportExport\SlideImportService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class SlideImportCommandTest extends TestCase
{
    public function testExecuteWithValidFile(): void
    {
        $importService = $this->createMock(SlideImportService::class);
        $importService->expects($this->once())
            ->method('import')
            ->willReturn(new ImportResult(2, []));

        $command = new SlideImportCommand($importService);

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

    public function testExecuteWithInvalidFile(): void
    {
        $importService = $this->createMock(SlideImportService::class);
        $importService->expects($this->never())->method('import');

        $command = new SlideImportCommand($importService);

        $input = new ArrayInput([
            'file' => '/nonexistent/file.jsonl',
        ]);
        $output = new BufferedOutput();

        $exitCode = $command->run($input, $output);

        $this->assertSame(2, $exitCode);
        $this->assertStringContainsString('File not found', $output->fetch());
    }

    public function testExecuteWithErrors(): void
    {
        $importService = $this->createMock(SlideImportService::class);
        $importService->expects($this->once())
            ->method('import')
            ->willReturn(new ImportResult(1, ['Error: Invalid line']));

        $command = new SlideImportCommand($importService);

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
