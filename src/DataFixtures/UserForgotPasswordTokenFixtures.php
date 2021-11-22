<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserForgotPasswordToken;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserForgotPasswordTokenFixtures extends Fixture implements OrderedFixtureInterface
{

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference('user@mail.com_user');

        $emailToken = UserForgotPasswordToken::create($user);

        $expiredEmailToken = UserForgotPasswordToken::create($user);
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
        return UserEmailTokenFixtures::getOrderNumber() + 1;
    }

}