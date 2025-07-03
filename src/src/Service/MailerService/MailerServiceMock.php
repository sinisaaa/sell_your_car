<?php

declare(strict_types=1);

namespace App\Service\MailerService;

use Symfony\Component\Mime\Email;

final class MailerServiceMock implements LocalMailerInterface
{

    /**
     * @param Email $email
     */
    public function sendEmail(Email $email): void
    {
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
        return new Email();
    }
}