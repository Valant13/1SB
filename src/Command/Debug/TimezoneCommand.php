<?php

namespace App\Command\Debug;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TimezoneCommand extends Command
{
    protected static $defaultName = 'debug:timezone';

    /**
     *
     */
    protected function configure(): void
    {
        $this->setDescription('Show server timezone');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dateTime = new \DateTime();

        $output->writeln('Timezone: ' . $dateTime->getTimezone()->getName());
        $output->writeln('Current time: ' . $dateTime->format('Y-m-d H:i:s'));

        return Command::SUCCESS;
    }
}
