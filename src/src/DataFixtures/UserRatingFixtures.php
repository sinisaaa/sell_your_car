<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserRating;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class UserRatingFixtures extends Fixture implements OrderedFixtureInterface
{

    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        /** @var User $ratedUser */
        $ratedUser = $this->getReference('carDealerForRatings@mail.com_user');

        /** @var User  $user */
        $user = $this->getReference('user@mail.com_user');

        $userRating = new UserRating();
        $userRating->setUser($user);
        $userRating->setRatedUser($ratedUser);
        $userRating->setRating(2);

        $manager->persist($userRating);
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
        return ArticleFixtures::getOrderNumber() + 1;
    }

}