<?php

declare(strict_types=1);

namespace App\Service\MailerService;

use App\Helper\EnvironmentHelper;
use App\Helper\Mailer\EmailMessage;
use RuntimeException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class MailerService implements LocalMailerInterface
{

    /**
     * MailerService constructor.
     * @param MailerInterface $mailer
     * @param EnvironmentHelper $environmentHelper
     * @param string $fromEmail
     * @param string $fromEmailName
     */
    public function __construct(
        private MailerInterface $mailer,
        private EnvironmentHelper $environmentHelper,
        private string $fromEmail,
        private string $fromEmailName,
    )
    {
        if ($this->environmentHelper->isInTestMode()) {
            throw new RuntimeException('Mailer service can not be initialized in test environment');
        }
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