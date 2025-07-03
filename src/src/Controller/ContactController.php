<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\Entity\Contact;
use App\Entity\RecordedEvent;
use App\Event\ContactMessageCreatedEvent;
use App\Form\Type\ContactCreateType;
use App\Service\RecordedEventService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View as ApiView;
use SimpleBus\SymfonyBridge\Bus\EventBus;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ContactController extends BaseController
{

    /**
     * ContactController constructor.
     * @param EntityManagerInterface $em
     * @param RecordedEventService $recordedEventService
     * @param TranslatorInterface $translator
     * @param EventBus $eventBus
     */
    public function __construct(
        private EntityManagerInterface $em,
        private RecordedEventService $recordedEventService,
        private TranslatorInterface $translator,
        private EventBus $eventBus
    )
    {
    }

    /**
     * @SWG\Post(
     *     path="/api/public/contacts",
     *     summary="Creates contact message",
     *     description="Creates contact message",
     *     tags={"Contacts"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Created contact",
     *     @Model(type=Contact::class, groups={"contact.get"}),
     *     ),
     *     @SWG\Response(
     *          response = 400,
     *          description="Form validation error"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *     ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="article",
     *          @Model(type=ContactCreateType::class)
     *     )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"contact.get"})
     * @Route("/api/public/contacts", methods={"POST"})
     *
     * @param Request $request
     * @return ApiView
     *
     */
    public function createAction(Request $request): ApiView
    {
        $form = $this->createForm(ContactCreateType::class);
        $form->submit($request->request->all());

        if ($this->recordedEventService->hasRecordedEvents(RecordedEvent::CONTACT_EVENT)) {
            throw new ConflictHttpException($this->translator->trans('Exception.Contact.Wait.Time'));
        }

        if ($this->getParameter('security_question_answer') !== $request->request->get('securityQuestion')) {
            throw new BadRequestHttpException($this->translator->trans('Exception.Auth.Register.Security.Answer.Invalid'));
        }

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        /** @var Contact $contact */
        $contact = $form->getData();

        $this->em->persist($contact);
        $this->em->flush();

        $this->eventBus->handle(new ContactMessageCreatedEvent());

        return ApiView::create($contact);
    }

}