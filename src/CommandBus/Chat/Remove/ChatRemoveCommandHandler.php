<?php

declare(strict_types=1);

namespace App\CommandBus\Chat\Remove;

use App\Entity\Chat;
use App\Security\Voters\CanAccessChatVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ChatRemoveCommandHandler
{

    /**
     * ChatRemoveCommandHandler constructor.
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(private EntityManagerInterface $em, private TranslatorInterface $translator, private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    /**
     * @param ChatRemoveCommand $command
     */
    public function handle(ChatRemoveCommand $command): void
    {
        $ids = $command->getIds();
        $user = $command->getUser();

        foreach ($ids as $chatId) {
            $chat = $this->getChat((int)$chatId);

            if (false === $this->authorizationChecker->isGranted(CanAccessChatVoter::CAN_ACCESS_CHAT, $chat)) {
                throw new AccessDeniedHttpException($this->translator->trans('Exception.Chat.User.Not.Have.Permissions'));
            }

            if ($user === $chat->getSender()) {
                $chat->setDeletedBySender(true);
            }

            if ($user === $chat->getReceiver()) {
                $chat->setDeletedByReceiver(true);
            }

            $this->em->persist($chat);
        }
    }

    /**
     * @param int $chatId
     * @return Chat
     */
    private function getChat(int $chatId): Chat
    {
        $chat = $this->em->getRepository(Chat::class)->find($chatId);

        if (null === $chat) {
            throw new NotFoundHttpException($this->translator->trans('Exception.Chat.Chat.Not.Found'));
        }

        return $chat;
    }

}