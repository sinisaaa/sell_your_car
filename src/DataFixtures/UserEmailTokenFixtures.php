<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserEmailConfirmToken;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserEmailTokenFixtures extends Fixture implements OrderedFixtureInterface
{

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference('user@mail.com_user');

        $emailToken = UserEmailConfirmToken::create($user);

        $expiredEmailToken = UserEmailConfirmToken::create($user);
        $expiredEmailToken->setCreatedAt((new DateTime())->modify('-1 year'));

        $manager->persist($emailToken);
        $manager->persist($expiredEmailToken);

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
        return LocationFixtures::getOrderNumber() + 1;
    }

}