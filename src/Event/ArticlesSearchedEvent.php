<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\User;

final class ArticlesSearchedEvent
{

    /**
     * ArticlesSearchedEvent constructor.
     * @param User $user
     * @param string|null $url
     * @param array|null $parameters
     */
    public function __construct(private User $user, private ?string $url, private ?array $parameters)
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
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return array|null
     */
    public function getParameters(): ?array
    {
        return $this->parameters;
    }

}