<?php

declare(strict_types=1);

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class AdminChangeUserPasswordModel
{

    /**
     * @Assert\Length(
     *     min=8,
     *     minMessage="Password.Min.Length")
     * @Assert\Regex("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?!.*\s).+$/", message="Password.Requirement")
     */
    protected ?string $password = null;

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

}