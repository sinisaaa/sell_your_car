<?php

declare(strict_types=1);

namespace App\Helper;

final class EmailValidator
{

    /**
     * @param string|null $email
     * @return mixed
     */
    public static function isValidEmail(?string $email): mixed
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

}