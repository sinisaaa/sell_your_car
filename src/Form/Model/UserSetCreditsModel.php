<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

final class UserSetCreditsModel
{
    /**
     * @Assert\NotBlank
     */
    protected User $user;

    /**
     * @Assert\NotBlank
     */
    protected int $activeCredits;

    /**
     * @Assert\NotBlank
     */
    protected int $passiveCredits;

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getActiveCredits(): int
    {
        return $this->activeCredits;
    }

    /**
     * @param int $activeCredits
     */
    public function setActiveCredits(int $activeCredits): void
    {
        $this->activeCredits = $activeCredits;
    }

    /**
     * @return int
     */
    public function getPassiveCredits(): int
    {
        return $this->passiveCredits;
    }

    /**
     * @param int $passiveCredits
     */
    public function setPassiveCredits(int $passiveCredits): void
    {
        $this->passiveCredits = $passiveCredits;
    }

}