<?php

declare(strict_types=1);

namespace App\Service;

use App\Helper\Mailer\EmailMessage;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

final class MailerService
{

    /**
     * MailerService constructor.
     * @param MailerInterface $mailer
     * @param string $fromEmail
     * @param string $fromEmailName
     */
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private string $fromEmail,
        private string $fromEmailName
    )
    {
    }

    /**
     * @param Email $email
     * @throws TransportExceptionInterface
     */
    public function sendEmail(Email $email): void
    {
        $this->mailer->send($email);
    }

    /**
     * @param string $toAddress
     * @param string $subject
     * @param string $templateName
     * @param array $context
     * @return Email
     */
    public function createEmail(string $toAddress, string $subject, string $templateName, array $context = []): Email
    {
        return (new EmailMessage())->create(
            $toAddress,
            $this->fromEmailName .' <' . $this->fromEmail . '>',
            $subject,
            $context,
            $templateName
        );
    }
}