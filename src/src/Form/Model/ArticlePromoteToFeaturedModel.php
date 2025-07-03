<?php

declare(strict_types=1);

namespace App\Form\Model;

use DateTime;

final class ArticlePromoteToFeaturedModel
{

    /**
     * @var string
     */
    private string $numberOfDays;

    /**
     * @return string
     */
    public function getNumberOfDays(): string
    {
        return $this->numberOfDays;
    }

    /**
     * @param string $numberOfDays
     */
    public function setNumberOfDays(string $numberOfDays): void
    {
        $this->numberOfDays = $numberOfDays;
    }

}