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

        $manager->persist($roleAdmin);
        $manager->persist($roleUser);

        $this->addReference('role_admin', $roleAdmin);
        $this->addReference('role_user', $roleUser);

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
