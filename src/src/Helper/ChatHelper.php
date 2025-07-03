<?php

declare(strict_types=1);

namespace App\Helper;

use App\Entity\Chat;
use App\Entity\User;

final class ChatHelper
{

    /**
     * @param Chat $chat
     * @param User $user
     */
    public static function setSeenStatus(Chat $chat, User $user): void
    {
        if ($chat->getSender() === $user && false === $chat->allMessagesSeenBySender()) {
            $chat->setSeen(true);
        } else if ($chat->getReceiver() === $user && false === $chat->allMessagesSeenByReceiver()) {
            $chat->setSeen(true);
        }
    }

}