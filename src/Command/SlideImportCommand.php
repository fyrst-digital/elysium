<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Command;

use Blur\BlurElysiumSlider\Service\ImportExport\SlideImportService;
use League\Flysystem\FilesystemOperator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Feature;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'elysium:slides:import',
    description: 'Imports Elysium slides from a JSONL file. Supports absolute paths or relative paths from the default directory (elysium/).',
)]
class SlideImportCommand extends Command
{
    private const DEFAULT_IMPORT_PATH = 'elysium';

    public function __construct(
        private readonly SlideImportService $importService,
        private readonly FilesystemOperator $filesystem
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the .jsonl import file (absolute path or filename relative to elysium/)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!Feature::isActive('elysium_preview_import_export')) {
            $output->writeln('The import/export feature is not enabled.');

            return self::INVALID;
        }

        $filePath = $input->getArgument('file');
        $content = null;

        // Try absolute path first
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            if ($content === false) {
                $output->writeln('Failed to read the file.');
                return self::FAILURE;
            }
        } else {
            // Try relative path from default export directory
            $relativePath = self::DEFAULT_IMPORT_PATH . '/' . $filePath;
            if ($this->filesystem->fileExists($relativePath)) {
                $content = $this->filesystem->read($relativePath);
            } else {
                $output->writeln(sprintf('File not found at "%s" or "%s"', $filePath, $relativePath));
                return self::INVALID;
            }
        }

        $context = Context::createCLIContext();
        $result = $this->importService->import($content, $context);

        $output->writeln(sprintf('Imported %d slide(s).', $result->getImported()));

        foreach ($result->getErrors() as $error) {
            $output->writeln(sprintf('Error: %s', $error));
        }

        return empty($result->getErrors()) ? self::SUCCESS : self::FAILURE;
    }
}
