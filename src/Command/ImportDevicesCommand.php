<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Device;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportDevicesCommand extends Command
{

    /**
     * ImportDevicesCommand constructor.
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
        $this->setName('app:import-devices')
            ->setDescription('Imports devices.')
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
        $alreadyImported = count($this->em->getRepository(Device::class)->findAll()) > 0;
        if ($alreadyImported) {
            throw new Exception('Devuces already imported');
        }

        $devicesData = file_get_contents(__DIR__ . '/../../import/devices.xml');

        if (false === $devicesData) {
            throw new Exception('Devices can not be imported');
        }

        $devices = new SimpleXMLElement($devicesData);

        foreach ($devices as $device) {
            $userId = (int)$device->column[1];
            $deviceType = (string)$device->column[2];
            $token = (string)$device->column[2];

            $user = $this->em->getRepository(User::class)->find($userId);

            if (null === $user) {
                throw new Exception('User not found');
            }

            $device = new Device();
            $device->setUser($user)
                ->setDeviceType($deviceType)
                ->setToken($token);

            $this->em->persist($device);
        }

        $this->em->flush();

        $output->writeln('<fg=green>Devices successfully imported!</>');

        return Command::SUCCESS;
    }

}