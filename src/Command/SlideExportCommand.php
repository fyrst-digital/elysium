<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Command;

use Blur\BlurElysiumSlider\Service\ImportExport\SlideExportService;
use League\Flysystem\FilesystemOperator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Feature;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'elysium:slides:export',
    description: 'Exports Elysium slides to a JSONL file. By default, all slides are exported. Use --ids to export specific slides.',
)]
class SlideExportCommand extends Command
{
    private const DEFAULT_EXPORT_PATH = 'elysium';

    public function __construct(
        private readonly SlideExportService $exportService,
        private readonly FilesystemOperator $filesystem
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('ids', null, InputOption::VALUE_REQUIRED, 'Comma-separated list of slide IDs to export');
        $this->addOption('all', null, InputOption::VALUE_NONE, 'Export all slides (default behavior)');
        $this->addOption('output', null, InputOption::VALUE_REQUIRED, 'Output file path (if not provided, saves to the Shopware private filesystem)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!Feature::isActive('elysium_preview_import_export')) {
            $output->writeln('The import/export feature is not enabled.');

            return self::INVALID;
        }

        $context = Context::createCLIContext();

        $idsOption = $input->getOption('ids');
        $allOption = $input->getOption('all');

        if ($idsOption !== null && $allOption) {
            $output->writeln('Error: Cannot use both --ids and --all options together.');

            return self::INVALID;
        }

        if ($idsOption !== null) {
            $ids = array_filter(explode(',', $idsOption));
            $jsonl = $this->exportService->export($ids, $context);
        } else {
            $jsonl = $this->exportService->exportAll($context);
        }

        $outputPath = $input->getOption('output');

        if ($outputPath !== null) {
            $this->filesystem->write($outputPath, $jsonl);
            $output->writeln(sprintf('Exported slides to %s', $outputPath));
        } else {
            $timestamp = (new \DateTime())->format('Y-m-d-His');
            $filename = sprintf('elysium-slides-export-%s.jsonl', $timestamp);
            $outputPath = self::DEFAULT_EXPORT_PATH . '/' . $filename;

            if (!$this->filesystem->directoryExists(self::DEFAULT_EXPORT_PATH)) {
                $this->filesystem->createDirectory(self::DEFAULT_EXPORT_PATH);
            }

            $this->filesystem->write($outputPath, $jsonl);
            $output->writeln(sprintf('Exported slides to %s', $outputPath));
        }

        return self::SUCCESS;
    }
}
