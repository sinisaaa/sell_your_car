<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTFailureEventInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class JWTAuthListener.
 */
final class JWTAuthListener
{

    /**
     * JWTAuthListener constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @param JWTCreatedEvent $event
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();
        $payload = $event->getData();

        $payload['name'] = $user->getName();
        $payload['activeCredits'] = $user->getActiveCredits();
        $payload['passiveCredits'] = $user->getPassiveCredits();

        $event->setData($payload);
    }

    /**
     * @param JWTDecodedEvent $event
     */
    public function onTokenDecode(JWTDecodedEvent $event): void
    {
        $payload = $event->getPayload();

        /** @var User|null $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $payload['username'], 'active' => true]);

        if (null === $user) {
            $event->markAsInvalid();
        }
    }

    /**
     * @param JWTFailureEventInterface $event
     * @throws InvalidArgumentException
     */
    public function onTokenFailure(JWTFailureEventInterface $event): void
    {
        /** @var JWTAuthenticationFailureResponse $response */
        $response = $event->getResponse();

        if ($event instanceof JWTNotFoundEvent) {
            $response->setMessage('Token not set. Please provide one.');
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
        } elseif ($event instanceof JWTExpiredEvent) {
            $response->setMessage('Token has expired, please renew it.');
            $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
        } elseif ($event instanceof JWTInvalidEvent) {
            $response->setMessage('Token is invalid, please login again to get a new one.');
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }
    }

}
