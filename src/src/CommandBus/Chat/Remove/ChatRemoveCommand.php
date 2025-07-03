<?php

declare(strict_types=1);

namespace App\CommandBus\Chat\Remove;

use App\Entity\User;

final class ChatRemoveCommand
{

    /**
     * ChatRemoveCommand constructor.
     * @param string[] $ids
     * @param User $user
     */
    public function __construct(private array $ids, private User $user)
    {
    }

    /**
     * @return string[]
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

}