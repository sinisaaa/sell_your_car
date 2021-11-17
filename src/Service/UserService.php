<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Role;
use App\Entity\User;
use DateTime;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserService
{

    /**
     * UserService constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $plainPassword
     * @param Role $role
     * @param string|null $phone
     * @param string|null $address
     * @return User
     */
    public function createUser(
        string $email,
        string $firstName,
        string $lastName,
        string $plainPassword,
        Role $role,
        ?string $phone = null,
        ?string $address = null,
    ): User
    {
        $user = new User();
        $user
            ->setEmail($email)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setUsername($email)
            ->setPlainPassword($plainPassword)
            ->setCreatedOn(new DateTime())
            ->setAddress($address)
            ->setPhone($phone)
            ->addRole($role);

        return $user;
    }
}