<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Command;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Shopware\Core\Framework\Adapter\Console\ShopwareStyle;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Demodata\DemodataRequest;
use Shopware\Core\Framework\Demodata\DemodataService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommand(
    name: 'elysium:demodata',
    description: 'Generates demo data for Elysium slides.',
)]
class ElysiumDemodataCommand extends Command
{
    /**
     * @internal
     */
    public function __construct(
        private readonly DemodataService $demodataService,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly string $kernelEnv,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('slides', null, InputOption::VALUE_OPTIONAL, 'Number of Elysium slides to create', '5');
        $this->addOption('multiplier', null, InputOption::VALUE_OPTIONAL, 'Applies to --slides', '1');
        $this->addOption('reset-defaults', null, InputOption::VALUE_NONE, 'Set all counts to 0 unless specified');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->kernelEnv !== 'prod') {
            $output->writeln('Elysium demodata command requires the app environment set to production to run. Execute it with: `APP_ENV=prod bin/console elysium:demodata`');

            return self::INVALID;
        }

        $io = new ShopwareStyle($input, $output);
        $io->title('Elysium Slide Demodata Generator');

        $context = Context::createCLIContext();

        $request = new DemodataRequest();
        $request->multiplier = max((int) $input->getOption('multiplier'), 1);

        $slideCount = $this->getCount($input, 'slides');
        if (!$input->getOption('reset-defaults') || $input->getOption('slides') !== null) {
            $request->add(ElysiumSlidesDefinition::class, $slideCount);
        }

        $this->eventDispatcher->dispatch(new \Shopware\Core\Framework\Demodata\Event\DemodataRequestCreatedEvent($request, $context, $input));

        $demoContext = $this->demodataService->generate($request, $context, $io);

        $io->table(
            ['Entity', 'Items', 'Time'],
            $demoContext->getTimings()
        );

        return self::SUCCESS;
    }

    private function getCount(InputInterface $input, string $name): int
    {
        if ($input->getOption($name) !== null) {
            return (int) $input->getOption($name);
        }

        return 0;
    }
}
