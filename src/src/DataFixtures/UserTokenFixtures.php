<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserToken;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class UserTokenFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * UserTokenFixtures constructor.
     */
    public function __construct(private JWTTokenManagerInterface $tokenManager)
    {
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference('user@mail.com_user');
        $token = $this->tokenManager->create($user);
        $userToken = UserToken::create($token, $user);

        $expiredToken = $this->tokenManager->create($user);
        $expiredUserToken = UserToken::create($expiredToken, $user);
        $expiredUserToken->setGeneratedDate((new \DateTime())->modify('-1 year'));

        $manager->persist($expiredUserToken);
        $manager->persist($userToken);

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
        return UserFixtures::getOrderNumber() + 1;
    }
}
