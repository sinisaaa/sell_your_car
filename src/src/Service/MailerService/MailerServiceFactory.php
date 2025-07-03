<?php

declare(strict_types=1);

namespace App\Service\MailerService;

use App\Helper\EnvironmentHelper;
use Symfony\Component\Mailer\MailerInterface;

final class MailerServiceFactory
{

    /**
     * MailerFactory constructor.
     */
    public function __construct(
        private EnvironmentHelper $environmentHelper,
        private MailerInterface $mailer,
        private string $fromEmail,
        private string $fromEmailName
    )
    {
    }

    /**
     * @return LocalMailerInterface
     */
    public function makeMailerServiceService(): LocalMailerInterface
    {
        if ($this->environmentHelper->isInTestMode()) {
            return new MailerServiceMock();
        }

        return new MailerService($this->mailer, $this->environmentHelper, $this->fromEmail, $this->fromEmailName);
    }

}