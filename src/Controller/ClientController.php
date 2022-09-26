<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
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
//    private SerializerInterface $serializer;

    public function __construct(
        ClientRepository $clientRepository,
//        SerializerInterface $serializer
    )
    {
        $this->clientRepository = $clientRepository;
//        $this->serializer = $serializer;
    }

    #[Route('/', name: 'client.index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('client/index.html.twig', [
            'clients' => $this->clientRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'client.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
//        $newClient = $this->serializer->deserialize($request->getContent(),Client::class, 'json');
        $newClient = new Client();
        $form = $this->createForm(ClientType::class, $newClient);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($form->isValid()){
                $this->clientRepository->save($newClient, true);

//                return new JsonResponse(
//                    'Client added.',
//                    Response::HTTP_CREATED
//                );
                return $this->redirectToRoute('client.index', [], Response::HTTP_SEE_OTHER);

//                return new JsonResponse(
//                    $this->serializer->serialize($newClient, 'json'),
//                    Response::HTTP_CREATED
//                );
            }
        }

        return $this->renderForm('client/create.html.twig', [
            'client' => $newClient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'client.show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/{id}/edit', name: 'client.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->clientRepository->save($client, true);

            return $this->redirectToRoute('client.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('client/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'client.delete', methods: ['POST'])]
    public function delete(Request $request, Client $client): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $this->clientRepository->remove($client, true);
        }

        return $this->redirectToRoute('client.index', [], Response::HTTP_SEE_OTHER);
    }
}
