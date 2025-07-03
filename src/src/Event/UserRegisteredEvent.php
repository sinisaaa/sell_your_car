<?php

declare(strict_types=1);

namespace App\Event;

final class UserRegisteredEvent
{

    /**
     * UserRegisteredEvent constructor.
     * @param string $email
     * @param string $name
     * @param string $emailToken
     */
    public function __construct(
        private string $email,
        private string $name,
        private string $emailToken
    ){
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmailToken(): string
    {
        return $this->emailToken;
    }

}