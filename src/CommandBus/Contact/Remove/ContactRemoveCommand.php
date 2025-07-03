<?php

declare(strict_types=1);

namespace App\CommandBus\Contact\Remove;

final class ContactRemoveCommand
{

    /**
     * ContactRemoveCommand constructor.
     * @param array<int> $ids
     */
    public function __construct(private array $ids)
    {
    }

    /**
     * @return array<int>
     */
    public function getIds(): array
    {
        return $this->ids;
    }

}