<?php

namespace App\Helper\QueryFilter\Interfaces;

interface SupportsType
{

    /**
     * @param string $type
     * @return bool
     */
    public function supportsType(string $type): bool;
}