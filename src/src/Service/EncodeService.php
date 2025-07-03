<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class EncodeService
{

    /**
     * EncodeService constructor.
     * @param UserPasswordHasherInterface $passwordEncoder
     */
    public function __construct(private UserPasswordHasherInterface $passwordEncoder)
    {
    }

    /**
     * @param User $user
     */
    public function encodeUserPassword(User $user): void
    {
        if (!$user->getPlainPassword()) {
            return;
        }

        $user->setSalt(base_convert(sha1(uniqid((string)mt_rand(), true)), 16, 36));
        $encoded = $this->passwordEncoder->hashPassword(
            $user,
            $user->getPlainPassword()
        );
        $user->setPassword($encoded);
    }

    /**
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function isPasswordValid(User $user, string $password): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $password);
    }

}