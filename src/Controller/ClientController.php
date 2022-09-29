<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\DBAL\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/client')]
class ClientController extends AbstractController
{
    private ClientRepository $clientRepository;
    private SerializerInterface $serializer;

    public function __construct(
        ClientRepository    $clientRepository,
        SerializerInterface $serializer
    )
    {
        $this->clientRepository = $clientRepository;
        $this->serializer = $serializer;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'client.index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($this->clientRepository->findAll(), 'json'),
            Response::HTTP_OK
        );
    }

    #[Route('/create', name: 'client.create', methods: 'POST')]
    public function create(Request $request): JsonResponse
    {
        $client = $this->serializer->deserialize($request->getContent(), Client::class, 'json');

        try {
            $this->clientRepository->save($client, true);

            return new JsonResponse(
                $this->serializer->serialize($client, 'json'),
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

    #[Route('/{id}', name: 'client.show', methods: 'GET')]
    public function show(Client $client): Response
    {
        return new JsonResponse(
            $this->serializer->serialize($client, 'json'),
            Response::HTTP_OK
        );
    }

    #[Route('/{id}/edit', name: 'client.edit', methods: 'PATCH')]
    public function edit(Request $request, Client $client): JsonResponse
    {
        try {
            $client = $this->clientRepository->find($client->getId());

            if (!$client) {
                throw $this->createNotFoundException(
                    'No client found for id ' . $client->getId()
                );
            }

            $data = $this->serializer->deserialize(
                $request->getContent(), Client::class, 'json'
            );

            $client->setFirstName($data->getFirstName());
            $client->setLastName($data->getLastName());
            $client->setEmail($data->getEmail());
            $client->setPhoneNumber($data->getPhoneNumber());

            $this->clientRepository->save($client, true);

            return new JsonResponse(
                $this->serializer->serialize($client, 'json'),
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            //@TODO: add loger
            return new JsonResponse(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('/{id}', name: 'client.delete', methods: 'DELETE')]
    public function delete(Client $client): JsonResponse
    {
        try {
            $this->clientRepository->remove($client, true);

            return new JsonResponse(
                'Client removed.',
                Response::HTTP_NO_CONTENT
            );
        } catch (Exception $e) {
            //@TODO: add loger
            return new JsonResponse(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
