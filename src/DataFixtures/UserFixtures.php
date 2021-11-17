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
                'firstName' => 'Regular',
                'lastName' => 'User',
                'role_reference_name' => 'role_user',
            ],
        ];

        foreach ($users as $user) {
            $this->createUser(
                $manager,
                $user['email'],
                $user['firstName'],
                $user['lastName'],
                $user['role_reference_name']
            );
        }
    }

    /**
     * @param ObjectManager $manager
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $roleReferenceName
     */
    private function createUser(
        ObjectManager $manager,
        string $email,
        string $firstName,
        string $lastName,
        string $roleReferenceName,
    ): void
    {
        /** @var Role $role */
        $role = $this->getReference($roleReferenceName);
        $user = $this->userService->createUser($email, $firstName, $lastName, '123qweQWE', $role);

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
        return 2;
    }
}
