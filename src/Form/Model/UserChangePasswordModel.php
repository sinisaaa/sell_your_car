<?php

declare(strict_types=1);

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class UserChangePasswordModel
{

    /**
     * @Assert\Length(
     *     min=8,
     *     minMessage="Password.Min.Length")
     * @Assert\Regex("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?!.*\s).+$/", message="Password.Requirement")
     */
    protected ?string $password = null;

    /**
     * @var string|null
     */
    protected ?string $token = null;

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

}