<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\UserForgotPasswordEvent;
use App\Event\UserRegisteredEvent;
use App\Service\MailerService\MailerServiceFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class NotificationsListener
{

    /**
     * NotificationsListener constructor.
     * @param ContainerInterface $container
     * @param MailerServiceFactory $mailerService
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private ContainerInterface $container,
        private MailerServiceFactory $mailerService,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @param UserRegisteredEvent $event
     */
    public function sendUserCreatedNotifications(UserRegisteredEvent $event): void
    {
        $frontendURL = $this->container->getParameter('frontend_url');
        $mail = $this->mailerService->makeMailerServiceService()->createEmail(
            $event->getEmail(),
            $this->translator->trans('Email.Messages.Registration.Confirm.Email.Address.Title'),
            'register_confirm.html.twig',
            ['name' => $event->getName(), 'link' => $frontendURL . '/auth/potvrdi-email?token=' . $event->getEmailToken()]
        );

        $this->mailerService->makeMailerServiceService()->sendEmail($mail);
    }

    /**
     * @param UserForgotPasswordEvent $event
     */
    public function sendForgotPasswordNotifications(UserForgotPasswordEvent $event): void
    {
        $frontendURL = $this->container->getParameter('frontend_url');
        $mail = $this->mailerService->makeMailerServiceService()->createEmail(
            $event->getUser()->getEmail(),
            $this->translator->trans('Email.Messages.User.Forgot.Password.Title'),
            'forgot_password.html.twig',
            ['name' => $event->getUser()->getName(), 'link' => $frontendURL . '/auth/restart-lozinke?token=' . $event->getForgotPasswordToken()]
        );

        $this->mailerService->makeMailerServiceService()->sendEmail($mail);
    }

}