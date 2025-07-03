<?php

declare(strict_types=1);

namespace App\CommandBus\Contact\Remove;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactRemoveCommandHandler
{

    /**
     * ContactRemoveCommandHandler constructor.
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private EntityManagerInterface $em,
        private TranslatorInterface $translator
    )
    {
    }

    /**
     * @param ContactRemoveCommand $command
     */
    public function handle(ContactRemoveCommand $command): void
    {
        foreach ($command->getIds() as $contactId) {
            $contact = $this->em->getRepository(Contact::class)->find($contactId);

            if (null === $contact) {
                throw new NotFoundHttpException($this->translator->trans('Exception.Contact.Message.Not.Found'));
            }

            $this->em->remove($contact);
        }
    }

}