<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

class ImportLocationsCommand extends Command
{

    /**
     * ImportLocationsCommand constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setName('app:import-locations')
            ->setDescription('Imports locations.')
            ->setHidden(true);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $alreadyImported = count($this->em->getRepository(Location::class)->findAll()) > 0;
        if ($alreadyImported) {
            throw new Exception('Locations already imported');
        }

        $locationsData = file_get_contents(__DIR__ . '/../../import/locations.xml');
        if (false === $locationsData) {
            throw new Exception('Locations can not be imported');
        }

        $locations = new SimpleXMLElement($locationsData);

        foreach ($locations as $location) {
            $location = Location::create((string)$location->name);
            $this->em->persist($location);
        }

        $this->em->flush();

        $output->writeln('<fg=green>Locations successfully imported!</>');

        return Command::SUCCESS;
    }

}