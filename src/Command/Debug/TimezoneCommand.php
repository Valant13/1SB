<?php

namespace App\Command\Debug;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TimezoneCommand extends Command
{
    protected static $defaultName = 'debug:timezone';

    protected function configure(): void
    {
        $this->setDescription('Show server timezone');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln((new \DateTime())->getTimezone()->getName());

        return Command::SUCCESS;
    }
}
