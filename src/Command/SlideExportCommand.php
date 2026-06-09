<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Command;

use Blur\BlurElysiumSlider\Service\ImportExport\SlideExportService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Feature;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'elysium:slides:export',
    description: 'Exports Elysium slides to a JSONL file.',
)]
class SlideExportCommand extends Command
{
    public function __construct(
        private readonly SlideExportService $exportService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('ids', null, InputOption::VALUE_REQUIRED, 'Comma-separated list of slide IDs to export');
        $this->addOption('output', null, InputOption::VALUE_REQUIRED, 'Output file path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!Feature::isActive('elysium_preview_import_export')) {
            $output->writeln('The import/export feature is not enabled.');

            return self::INVALID;
        }

        $context = Context::createCLIContext();

        $idsOption = $input->getOption('ids');
        if ($idsOption !== null) {
            $ids = array_filter(explode(',', $idsOption));
            $jsonl = $this->exportService->export($ids, $context);
        } else {
            $jsonl = $this->exportService->exportAll($context);
        }

        $outputPath = $input->getOption('output');

        if ($outputPath !== null) {
            file_put_contents($outputPath, $jsonl);
            $output->writeln(sprintf('Exported slides to %s', $outputPath));
        } else {
            $output->writeln($jsonl);
        }

        return self::SUCCESS;
    }
}
