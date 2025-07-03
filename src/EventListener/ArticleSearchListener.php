<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Search;
use App\Event\ArticlesSearchedEvent;
use Doctrine\ORM\EntityManagerInterface;

final class ArticleSearchListener
{

    /**
     * ArticleSearchListener constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @param ArticlesSearchedEvent $event
     */
    public function articlesSearched(ArticlesSearchedEvent $event): void
    {
        $search = new Search();
        $search->setUser($event->getUser());
        $search->setUrl($event->getUrl());
        $search->setParameters($event->getParameters());

        $this->em->persist($search);
        $this->em->flush();
    }

}