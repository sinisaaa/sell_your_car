<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Role;
use App\Service\EncodeService;
use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{

    /**
     * UserFixtures constructor.
     * @param UserService $userService
     * @param EncodeService $encodeService
     */
    public function __construct(
        private UserService $userService,
        private EncodeService $encodeService
    )
    {
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'email' => 'user@mail.com',
                'name' => 'Regular User',
                'role_reference_name' => 'role_user',
                'active' => true
            ],
            [
                'email' => 'logout@mail.com',
                'name' => 'Logout User',
                'role_reference_name' => 'role_user',
                'active' => true
            ],
            [
                'email' => 'inactive@mail.com',
                'name' => 'Inactive User',
                'role_reference_name' => 'role_user',
                'active' => false
            ],
            [
                'email' => 'sender@mail.com',
                'name' => 'Email Sender',
                'role_reference_name' => 'role_user',
                'active' => true
            ],
        ];

        foreach ($users as $user) {
            $this->createUser(
                $manager,
                $user['email'],
                $user['name'],
                $user['role_reference_name'],
                $user['active']
            );
        }
    }

    /**
     * @param ObjectManager $manager
     * @param string $email
     * @param string $name
     * @param string $roleReferenceName
     * @param bool $active
     */
    private function createUser(
        ObjectManager $manager,
        string $email,
        string $name,
        string $roleReferenceName,
        bool $active
    ): void
    {
        /** @var Role $role */
        $role = $this->getReference($roleReferenceName);
        $user = $this->userService->createUser($email, $name, '123qweQWE', $role);
        $user->setActive($active);

        $this->encodeService->encodeUserPassword($user);

        $manager->persist($user);
        $manager->flush();

        $this->addReference($user->getUsername(). '_user', $user);
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
        return RoleFixtures::getOrderNumber() + 1;
    }
}
