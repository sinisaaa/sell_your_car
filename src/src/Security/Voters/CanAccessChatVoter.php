<?php

declare(strict_types=1);

namespace App\Security\Voters;

use App\Entity\Chat;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CanAccessChatVoter extends Voter
{

    public const CAN_ACCESS_CHAT = 'CAN_ACCESS_CHAT';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject): bool
    {
        return self::CAN_ACCESS_CHAT === $attribute;
    }

    /**
     * @param string $attribute
     * @param Chat $subject
     * @param TokenInterface $token
     * @return bool
     *
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        if ($user === $subject->getReceiver() || $user === $subject->getSender()) {
            return true;
        }

        return false;
    }

}