<?php

declare(strict_types=1);

namespace App\Command;

use Gregwar\Image\GarbageCollect;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteOldImagesFromCacheCommand extends Command
{

    /**
     *
     */
    protected function configure(): void
    {
        $this->setName('app:delete-image-cache')
            ->setDescription('Deletes images older than 30 days from cache.')
            ->setHelp('Deletes images older than 30 days from cache..');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        GarbageCollect::dropOldFiles(__DIR__.'/../../cache', 30, true);
        $output->writeln('<fg=green>Images successfully deleted!</>');

        return Command::SUCCESS;
    }

}