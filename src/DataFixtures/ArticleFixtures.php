<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\ArticleCategory;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture implements OrderedFixtureInterface
{

    /**
     * ArticleFixtures constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $articles = [
            [
                'title' => 'test title',
                'user_reference' => 'user@mail.com_user',
                'urgent' => false,
                'fixed' => false,
                'negotiable' => false,
                'featured_pending' => false,
                'discontinued' => false,
                'featuredFrom' => null,
                'featuredTo' => null,
            ],
            [
                'title' => 'test urgent',
                'user_reference' => 'user@mail.com_user',
                'urgent' => true,
                'fixed' => false,
                'negotiable' => false,
                'featured_pending' => false,
                'discontinued' => false,
                'featuredFrom' => null,
                'featuredTo' => null,
            ],
            [
                'title' => 'test fixed',
                'user_reference' => 'user@mail.com_user',
                'urgent' => false,
                'fixed' => true,
                'negotiable' => false,
                'featured_pending' => false,
                'discontinued' => false,
                'featuredFrom' => null,
                'featuredTo' => null,
            ],
            [
                'title' => 'test negotiable',
                'user_reference' => 'user@mail.com_user',
                'urgent' => false,
                'fixed' => false,
                'negotiable' => true,
                'featured_pending' => false,
                'discontinued' => false,
                'featuredFrom' => null,
                'featuredTo' => null,
            ],
            [
                'title' => 'test featured pending',
                'user_reference' => 'user@mail.com_user',
                'urgent' => false,
                'fixed' => false,
                'negotiable' => false,
                'featured_pending' => true,
                'discontinued' => false,
                'featuredFrom' => null,
                'featuredTo' => null,
            ],
            [
                'title' => 'test discontinued',
                'user_reference' => 'user@mail.com_user',
                'urgent' => false,
                'fixed' => false,
                'negotiable' => false,
                'featured_pending' => false,
                'discontinued' => true,
                'featuredFrom' => null,
                'featuredTo' => null,
            ],
            [
                'title' => 'test featured',
                'user_reference' => 'user@mail.com_user',
                'urgent' => false,
                'fixed' => false,
                'negotiable' => false,
                'featured_pending' => false,
                'discontinued' => false,
                'featuredFrom' => (new DateTime())->modify('-1 days'),
                'featuredTo' => (new DateTime())->modify('+1 days')
            ],
            [
                'title' => 'testDeactivate',
                'user_reference' => 'user@mail.com_user',
                'urgent' => false,
                'fixed' => false,
                'negotiable' => false,
                'featured_pending' => false,
                'discontinued' => true,
                'featuredFrom' => null,
                'featuredTo' => null,
            ],
            [
                'title' => 'testDeactivateOtherUser',
                'user_reference' => 'logout@mail.com_user',
                'urgent' => false,
                'fixed' => false,
                'negotiable' => false,
                'featured_pending' => false,
                'discontinued' => true,
                'featuredFrom' => null,
                'featuredTo' => null,
            ],
            [
                'title' => 'testSell',
                'user_reference' => 'user@mail.com_user',
                'urgent' => false,
                'fixed' => false,
                'negotiable' => false,
                'featured_pending' => false,
                'discontinued' => true,
                'featuredFrom' => null,
                'featuredTo' => null,
            ],
            [
                'title' => 'testSellOtherUser',
                'user_reference' => 'logout@mail.com_user',
                'urgent' => false,
                'fixed' => false,
                'negotiable' => false,
                'featured_pending' => false,
                'discontinued' => true,
                'featuredFrom' => null,
                'featuredTo' => null,
            ],
        ];

        foreach ($articles as $article) {
            $this->createArticle(
                $article['title'],
                $article['user_reference'],
                $article['urgent'],
                $article['fixed'],
                $article['negotiable'],
                $article['featured_pending'],
                $article['discontinued'],
                $article['featuredFrom'],
                $article['featuredTo'],
            );
        }

    }

    /**
     * @param string $title
     * @param string $userReference
     * @param bool $urgent
     * @param bool $fixed
     * @param bool $negotiable
     * @param bool $featuredPending
     * @param bool $discontinued
     * @param DateTime|null $featuredFrom
     * @param DateTime|null $featuredTo
     */
    private function createArticle(
        string $title,
        string $userReference,
        bool $urgent,
        bool $fixed,
        bool $negotiable,
        bool $featuredPending,
        bool $discontinued,
        ?DateTime $featuredFrom,
        ?DateTime $featuredTo
    ): void
    {
        /** @var User $user */
        $user = $this->getReference($userReference);

        /** @var ArticleCategory $articleCategory */
        $articleCategory = $this->getReference('default_article_category');

        $article = new Article();
        $article->setTitle($title);
        $article->setUrgent($urgent);
        $article->setFixed($fixed);
        $article->setNegotiable($negotiable);
        $article->setFeaturedPending($featuredPending);
        $article->setDiscontinued($discontinued);
        $article->setCreatedAt(new DateTime());
        $article->setFeaturedFrom($featuredFrom);
        $article->setFeaturedTo($featuredTo);
        $article->setUser($user);
        $article->setIsDraft(false);
        $article->setCategory($articleCategory);

        $this->em->persist($article);
        $this->em->flush();
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
        return ArticleCategoryFixtures::getOrderNumber() + 1;
    }

}