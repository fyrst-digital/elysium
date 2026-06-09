<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Command;

use Blur\BlurElysiumSlider\Service\ImportExport\SlideImportService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Feature;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'elysium:slides:import',
    description: 'Imports Elysium slides from a JSONL file.',
)]
class SlideImportCommand extends Command
{
    public function __construct(
        private readonly SlideImportService $importService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the .jsonl import file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!Feature::isActive('elysium_preview_import_export')) {
            $output->writeln('The import/export feature is not enabled.');

            return self::INVALID;
        }

        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            $output->writeln(sprintf('File not found: %s', $filePath));

            return self::INVALID;
        }

        $content = file_get_contents($filePath);

        if ($content === false) {
            $output->writeln('Failed to read the file.');

            return self::FAILURE;
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
