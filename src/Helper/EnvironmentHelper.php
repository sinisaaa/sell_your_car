<?php

declare(strict_types=1);

namespace App\Helper;

final class EnvironmentHelper
{

    public const ENV_TEST = 'test';
    public const ENV_PROD = 'prod';

    /**
     * EnvironmentHelper constructor.
     * @param string $environment
     */
    public function __construct(private string $environment)
    {
    }

    /**
     * @return string
     */
    public function getCurrentEnv(): string
    {
        return $this->environment;
    }

    /**
     * @return bool
     */
    public function isInTestMode(): bool
    {
        return $this->environment === self::ENV_TEST;
    }

    /**
     * @return bool
     */
    public function isInProductionMode(): bool
    {
        return $this->environment === self::ENV_PROD;
    }
}