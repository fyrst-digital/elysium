<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Command;

use Blur\BlurElysiumSlider\Service\SlideCoverImageSwitchService;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'elysium:slides:switch-cover-images',
    description: 'Switches the desktop cover image to the mobile cover image for all slides that do not have a mobile cover image.',
)]
class SlideCoverImageSwitchCommand extends Command
{
    public function __construct(
        private readonly SlideCoverImageSwitchService $switchService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = Context::createCLIContext();

        $affected = $this->switchService->switch($context);

        $output->writeln(sprintf('Switched cover images for %d slide(s).', $affected));

        return self::SUCCESS;
    }
}
