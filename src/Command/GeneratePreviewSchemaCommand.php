<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Command;

use Blur\BlurElysiumSlider\Preview\PreviewSchemaRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'elysium:preview-schema:generate',
    description: 'Generates preview schema files for storefront and administration from the PHP source of truth.'
)]
class GeneratePreviewSchemaCommand extends Command
{
    public function __construct(
        private readonly PreviewSchemaRegistry $previewSchemaRegistry,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $schema = $this->previewSchemaRegistry->get('blur-elysium-slide');
        if ($schema === null) {
            $io->error('Schema "blur-elysium-slide" not found in registry.');

            return Command::FAILURE;
        }

        $data = $schema->toArray();

        $this->writeStorefrontSchema($data);
        $this->writeAdministrationSchema($data);

        $io->success('Preview schema files generated successfully.');

        return Command::SUCCESS;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function writeStorefrontSchema(array $data): void
    {
        $content = "/* eslint-disable */\n";
        $content .= "// This file is auto-generated. Do not edit manually.\n";
        $content .= "// Run `bin/console elysium:preview-schema:generate` to regenerate.\n";
        $content .= "export const previewSchema = " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . ";\n";

        $target = __DIR__ . '/../../src/Resources/app/storefront/src/js/schema/slide-preview.js';
        @mkdir(dirname($target), 0777, true); // @phpstan-ignore shopware.forbidLocalDiskWrite
        file_put_contents($target, $content); // @phpstan-ignore shopware.forbidLocalDiskWrite
    }

    /**
     * @param array<string, mixed> $data
     */
    private function writeAdministrationSchema(array $data): void
    {
        $content = "/* eslint-disable */\n";
        $content .= "// This file is auto-generated. Do not edit manually.\n";
        $content .= "// Run `bin/console elysium:preview-schema:generate` to regenerate.\n";
        $content .= "import type { PreviewSchema } from '../composables/preview-schema';\n\n";
        $content .= "export const previewSchema: PreviewSchema = " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . " as PreviewSchema;\n";

        $target = __DIR__ . '/../../src/Resources/app/administration/src/schema/slide-preview.ts';
        @mkdir(dirname($target), 0777, true); // @phpstan-ignore shopware.forbidLocalDiskWrite
        file_put_contents($target, $content); // @phpstan-ignore shopware.forbidLocalDiskWrite
    }
}
