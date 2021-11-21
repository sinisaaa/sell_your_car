<?php

declare(strict_types=1);

namespace App\Helper\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

final class EmailMessage extends TemplatedEmail
{
    /**
     * @param string $to
     * @param string $from
     * @param string $subject
     * @param array<mixed> $context
     * @param string $template
     * @return $this
     */
    public function create(string $to, string $from, string $subject, array $context, string $template): EmailMessage
    {
        $this->to($to)
            ->from($from)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        return $this;
    }
}
