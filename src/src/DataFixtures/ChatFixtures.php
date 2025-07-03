<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Chat;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ChatFixtures extends Fixture implements OrderedFixtureInterface
{

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        /** @var User $userReceiver */
        $userReceiver = $this->getReference('user@mail.com_user');
        /** @var User  $userSender */
        $userSender = $this->getReference('sender@mail.com_user');

        $chat = new Chat();
        $chat->setSender($userSender);
        $chat->setReceiver($userReceiver);
        $chat->setSubject('Chat between logged in user and message sender');
        $chat->setCreatedAt(new \DateTime());

        $chatWithNoAccess = new Chat();
        $chatWithNoAccess->setSender($userSender);
        $chatWithNoAccess->setReceiver($userSender);
        $chatWithNoAccess->setSubject('Chat same user');
        $chatWithNoAccess->setCreatedAt(new \DateTime());

        $chatForDelete = new Chat();
        $chatForDelete->setSender($userReceiver);
        $chatForDelete->setReceiver($userReceiver);
        $chatForDelete->setSubject('Chat for delete');
        $chatForDelete->setCreatedAt(new \DateTime());

        $manager->persist($chat);
        $manager->persist($chatWithNoAccess);
        $manager->persist($chatForDelete);
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
        return UserForgotPasswordTokenFixtures::getOrderNumber() + 1;
    }

}