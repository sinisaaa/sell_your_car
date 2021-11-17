<?php

declare(strict_types=1);

namespace App\Helper\ValueObjects;

use App\Helper\Exceptions\RoleCodeNotExistsException;

final class RoleCode
{
    public const ADMIN = 'ROLE_ADMIN';
    public const USER = 'ROLE_USER';

    /**
     * RoleCode constructor.
     * @param string $code
     *
     * @throws RoleCodeNotExistsException
     */
    private function __construct(private string $code)
    {
        if (false === self::isValidCode($this->code)) {
            throw new RoleCodeNotExistsException("Role code is not valid");
        }
    }

    /**
     * @param string $code
     * @return RoleCode
     *
     * @throws RoleCodeNotExistsException
     */
    public static function create(string $code): RoleCode
    {
        return new self($code);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return bool
     */
    private static function isValidCode(string $code): bool
    {
        return in_array($code, self::validCodes(), true);
    }

    /**
     * @return string[]
     */
    private static function validCodes(): array
    {
        return [
            self::ADMIN,
            self::USER
        ];
    }
}