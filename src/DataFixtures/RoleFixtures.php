<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Helper\ValueObjects\RoleCode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture implements OrderedFixtureInterface
{

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $roleAdmin = new Role(RoleCode::ADMIN, 'Admin');
        $roleUser = new Role(RoleCode::USER, 'User');
        $roleCarDealer = new Role(RoleCode::CAR_DEALER, 'Car Dealer');

        $manager->persist($roleAdmin);
        $manager->persist($roleUser);
        $manager->persist($roleCarDealer);

        $this->addReference('role_admin', $roleAdmin);
        $this->addReference('role_user', $roleUser);
        $this->addReference('role_car_dealer', $roleCarDealer);

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return self::getOrderNumber();
    }

    /**
     * @return int
     */
    public static function getOrderNumber(): int
    {
        return 1;
    }
}
