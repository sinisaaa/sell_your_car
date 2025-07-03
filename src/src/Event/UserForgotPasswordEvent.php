<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\User;

final class UserForgotPasswordEvent
{

    /**
     * UserForgotPasswordEvent constructor.
     */
    public function __construct(private User $user, private string $forgotPasswordToken)
    {
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getForgotPasswordToken(): string
    {
        return $this->forgotPasswordToken;
    }

}