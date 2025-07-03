<?php

declare(strict_types=1);

namespace App\Controller;

use App\CommandBus\Chat\Remove\ChatRemoveCommand;
use App\Controller\Base\BaseController;
use App\Entity\ChatMessage;
use App\Form\Type\ChatCreateType;
use App\Form\Type\ChatMessageCreateType;
use App\Helper\ChatHelper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\View\View as ApiView;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\Chat;
use Symfony\Contracts\Translation\TranslatorInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;

final class ChatController extends BaseController
{

    /**
     * ChatController constructor.
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @param PaginatorInterface $paginator
     * @param CommandBus $commandBus
     */
    public function __construct(
        private EntityManagerInterface $em,
        private TranslatorInterface $translator,
        private PaginatorInterface $paginator,
        private CommandBus $commandBus
    )
    {
    }

    /**
     * @SWG\Post(
     *     path="/api/chats",
     *     summary="Create chat beetween users",
     *     description="Create chat beetween users",
     *     tags={"Chats"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Created chat",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Chat::class, groups={"user.rel", "chat.get", "chat_sender.get","chat_receiver.get"})),
     *          )
     *     ),
     *     @SWG\Response(
     *          response = 400,
     *          description="Form validation error"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="chat",
     *          @Model(type=ChatCreateType::class)
     *     )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user.rel", "chat.get", "chat_sender.get","chat_receiver.get"})
     * @Route("/api/chats", methods={"POST"})
     *
     * @param Request $request
     * @return ApiView
     *
     * @throws Exception
     */
    public function createAction(Request $request): ApiView
    {
        $form = $this->createForm(ChatCreateType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        $body = $request->request->get('body');
        if (null === $body || '' === $body) {
            throw new BadRequestHttpException($this->translator->trans('Exception.Chat.Body.Empty'));
        }

        /** @var Chat $chat */
        $chat = $form->getData();
        $chat->setSender($this->getUser());
        $chat->setCreatedAt(new DateTime());

        $chatMessage = ChatMessage::create($chat, $chat->getSender(), $chat->getReceiver(), $body);

        $this->em->persist($chat);
        $this->em->persist($chatMessage);
        $this->em->flush();

        return ApiView::create($chat);
    }

    /**
     * @SWG\Post(
     *     path="/api/chats/{chat}",
     *     summary="Create chat message in chat",
     *     description="Create chat message in chat",
     *     tags={"Chats"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Updated chat",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Chat::class, groups={"user.rel", "chat.get", "chat_sender.get", "chat_receiver.get", "chat_message.get"})),
     *          )
     *     ),
     *     @SWG\Response(
     *          response = 400,
     *          description="Form validation error"
     *     ),
     *     @SWG\Response(
     *          response = 404,
     *          description="Chat not found"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="chat",
     *          @Model(type=ChatCreateType::class)
     *     )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user.rel", "chat.get", "chat_sender.get", "chat_receiver.get", "chat_message.get"})
     * @Route("/api/chats/{chat}", methods={"POST"})
     * @Security("is_granted('CAN_ACCESS_CHAT', chat)")
     *
     * @param Chat $chat
     * @param Request $request
     * @return ApiView
     *
     */
    public function createChatMessageAction(Chat $chat, Request $request): ApiView
    {
        $form = $this->createForm(ChatMessageCreateType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return ApiView::create($form);
        }


        /** @var ChatMessage $chatMessage */
        $chatMessage = $form->getData();

        if($chat->getReceiver() == $this->getUser()) {
            $receiver = $chat->getSender();
        }else {
            $receiver = $chat->getReceiver();
        }
        $chatMessage = ChatMessage::create($chat, $this->getUser(), $receiver, $chatMessage->getBody());
        $chat->setUpdatedAt(new \DateTime());
        $this->em->persist($chat);
        $this->em->persist($chatMessage);
        $this->em->flush();

        return ApiView::create($chat);
    }

    /**
     * @SWG\Get(
     *   path="/api/chats",
     *   summary="List of chats for current user",
     *   tags={"Chats"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of chats for current user",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Chat::class, groups={"user.rel", "chat.get", "chat_sender.get","chat_receiver.get"})),
     *          @SWG\Property(property="_meta", type="object", ref="#/definitions/MetaPagination"),
     *      )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Unauthorised"
     *   ),
     *   @SWG\Response(
     *      response="default",
     *      description="error"
     *   ),
     *   @SWG\Parameter(
     *      name="perPage",
     *      in="query",
     *      required=false,
     *      type="number",
     *      description="Number of items per page",
     *      @SWG\Schema(
     *         @SWG\Property(property="perPage", type="number")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="page",
     *      in="query",
     *      required=false,
     *      type="number",
     *      description="Currently selected page",
     *      @SWG\Schema(
     *         @SWG\Property(property="page", type="number")
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user.rel", "chat.get", "chat_sender.get", "chat_receiver.get"})
     * @Route("/api/chats", methods={"GET"})
     *
     * @param Request $request
     * @return array<mixed>
     */
    public function getAllChatsForUser(Request $request): array
    {
        $user = $this->getUser();
        $query = $this->em->getRepository(Chat::class)->findAllByUser($user);

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), $request->query->getInt('perPage', 10));
        $items = $pagination->getItems();

        /** @var Chat $item */
        foreach ($items as $item) {
            ChatHelper::setSeenStatus($item, $user);
        }

        return $this->getPaginatedItems($pagination, $items);
    }

    /**
     * @SWG\Get(
     *   path="/api/chats/{chat}",
     *   summary="Open chat and return all messages",
     *   tags={"Chats"},
     *   @SWG\Response(
     *     response=200,
     *     description="Open chat and return all messages",
     *     @Model(type=Chat::class, groups={"user.rel", "chat.get", "chat_sender.get", "chat_receiver.get", "chat_message.get"})
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Unauthorised"
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Chat not found"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="error"
     *   )
     * )
     * @ViewAnnotation(serializerGroups={"user.rel", "chat.get", "chat_sender.get", "chat_receiver.get", "chat_message.get"})
     * @Route("/api/chats/{chat}", requirements={"chat": "\d+"},  methods={"GET"})
     * @Security("is_granted('CAN_ACCESS_CHAT', chat)")
     *
     * @param Chat $chat
     * @return ApiView
     */
    public function openChatAction(Chat $chat): ApiView
    {
        $this->em->getRepository(ChatMessage::class)->markAllMessagesAsSeen($chat, $this->getUser());
        ChatHelper::setSeenStatus($chat, $this->getUser());

        return ApiView::create($chat);
    }

    /**
     * @SWG\Delete(
     *   path="/api/chats",
     *   summary="Mass remove chats by setting sender or receiver to null",
     *   tags={"Chats"},
     *   @SWG\Response(
     *     response=204,
     *     description="Chats removed",
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Unauthorised"
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Chat not found"
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="User does not have access to chat"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="error"
     *   ),
     *   @SWG\Parameter(
     *     name="ids",
     *     description="Every element in array represent chat id",
     *     type="array",
     *     in="body",
     *     @SWG\Items(type="integer"),
     *     collectionFormat="multi",
     *     @SWG\Schema(
     *        type="array",
     *        @SWG\Items(type="integer"),
     *        example= {1,2,3}
     *       )
     *    )
     * )
     * @ViewAnnotation(statusCode=204)
     * @Route("/api/chats", methods={"DELETE"})
     *
     * @param Request $request
     * @return ApiView
     */
    public function removeChatsAction(Request $request): ApiView
    {
        /** @var int[] $ids */
        $ids = $request->request->get('ids', []);

        $this->commandBus->handle(new ChatRemoveCommand($ids, $this->getUser()));

        return ApiView::create([], Response::HTTP_NO_CONTENT);
    }

}