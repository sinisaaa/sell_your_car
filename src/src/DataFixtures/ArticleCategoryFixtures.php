<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\ArticleCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ArticleCategoryFixtures extends Fixture implements OrderedFixtureInterface
{

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $articleCategory = new ArticleCategory();
        $articleCategory->setName('Default category');

        $this->addReference( 'default_article_category', $articleCategory);

        $manager->persist($articleCategory);
        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return self::getOrderNumber();
    }

    /**
     * @return int
     */
    public static function getOrderNumber(): int
    {
        return ChatFixtures::getOrderNumber() + 1;
    }
}