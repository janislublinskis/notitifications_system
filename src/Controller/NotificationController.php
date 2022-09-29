<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Message\SendEmailNotification;
use App\Repository\NotificationRepository;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/notification')]
class NotificationController extends AbstractController
{
    private SerializerInterface $serializer;
    private NotificationRepository $notificationRepository;
    private MessageBusInterface $bus;

    public function __construct(
        NotificationRepository $notificationRepository,
        SerializerInterface    $serializer,
        MessageBusInterface    $bus
    )
    {
        $this->notificationRepository = $notificationRepository;
        $this->serializer = $serializer;
        $this->bus = $bus;
    }

    #[Route('/', name: 'notification.index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($this->notificationRepository->findAll(), 'json'),
            Response::HTTP_OK
        );
    }

    #[Route('/create/{clientId}', name: 'notification.create', methods: 'POST')]
    public function create(Request $request): Response
    {
        try {
            $notifications = $this->serializer->deserialize(
                $request->getContent(), Notification::class . '[]', 'json'
            );

            foreach ($notifications as $notification) {
                $email = new SendEmailNotification($notification->getClientId()->getEmail(), $notification->getContent());
                $envelope = new Envelope($email, [new AmqpStamp('normal')]);
                $this->bus->dispatch($envelope);

                $this->notificationRepository->save($notification, true);
            }

            return new JsonResponse(
                $this->serializer->serialize($notifications, 'json'),
                Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            //@TODO: add loger
            return new JsonResponse(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('/{id}', name: 'notification.show', methods: 'GET')]
    public function show(Notification $notification): Response
    {
        return new JsonResponse(
            $this->serializer->serialize($notification, 'json'),
            Response::HTTP_OK
        );
    }
}
