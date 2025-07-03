<?php

declare(strict_types=1);

namespace App\Security\Voters;

use App\Entity\Article;
use App\Entity\Chat;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CanManageArticleVoter extends Voter
{

    public const CAN_MANAGE_ARTICLE = 'CAN_MANAGE_ARTICLE';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject): bool
    {
        return self::CAN_MANAGE_ARTICLE === $attribute;
    }

    /**
     * @param string $attribute
     * @param Article $subject
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

        if ($user->isAdmin() || $subject->getUser()->getId() === $user->getId()) {
            return true;
        }

        return false;
    }

}