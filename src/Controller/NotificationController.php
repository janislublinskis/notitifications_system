<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use App\Service\SendNotificationService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/notification')]
class NotificationController extends AbstractController
{
    private SendNotificationService $notificationService;
    private SerializerInterface $serializer;
    private NotificationRepository $notificationRepository;

    public function __construct(
        NotificationRepository  $notificationRepository,
        SendNotificationService $notificationService,
        SerializerInterface     $serializer
    )
    {
        $this->notificationRepository = $notificationRepository;
        $this->notificationService = $notificationService;
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'notification.index', methods: ['GET'])]
    public function index(NotificationRepository $notificationRepository): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($notificationRepository->findAll(), 'json'),
            Response::HTTP_OK
        );
    }

    #[Route('/create/{clientId}', name: 'notification.create', methods: 'POST')]
    public function create(Request $request): Response
    {
        try {
            $notification = $this->serializer->deserialize($request->getContent(), Notification::class . '[]', 'json');
            $this->notificationService->send($notification);
            $this->notificationRepository->save($notification, true);

            return new JsonResponse(
                $this->serializer->serialize($notification, 'json'),
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

    #[Route('/{id}', name: 'notification.show', methods: ['GET'])]
    public function show(Notification $notification): Response
    {
        return $this->render('notification/show.html.twig', [
            'notification' => $notification,
        ]);
    }

    #[Route('/{id}/edit', name: 'notification.edit', methods: 'POST')]
    public function edit(Request $request, Notification $notification, NotificationRepository $notificationRepository): Response
    {
        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notificationRepository->save($notification, true);

            return $this->redirectToRoute('notification.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('notification/edit.html.twig', [
            'notification' => $notification,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'notification.delete', methods: 'POST')]
    public function delete(Request $request, Notification $notification, NotificationRepository $notificationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $notification->getId(), $request->request->get('_token'))) {
            $notificationRepository->remove($notification, true);
        }

        return $this->redirectToRoute('notification.index', [], Response::HTTP_SEE_OTHER);
    }
}
