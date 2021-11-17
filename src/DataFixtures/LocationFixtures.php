<?php

namespace App\DataFixtures;

use App\Entity\Location;
use App\Entity\Region;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LocationFixtures extends Fixture implements OrderedFixtureInterface
{

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        /** @var Region $region */
        $region = $this->getReference('test_region');

        $testLocation = Location::create('Test City', $region);
        $manager->persist($testLocation);

        $this->addReference('test_location', $testLocation);

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 5;
    }
}
