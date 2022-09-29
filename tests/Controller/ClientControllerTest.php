<?php

namespace App\Tests\Controller;

use App\Entity\Client;
use App\Tests\Factory\AgentFactory;
use App\Tests\Factory\ApiTokenFactory;
use App\Tests\Factory\ClientFactory;

class ClientControllerTest extends BaseTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->path = '/api/clients';
        $this->repository = $this->entityManager->getRepository(Client::class);
    }

    public function testIndexUnauthorized(): void
    {
        static::createClient()->request('GET', $this->path);
        $this->assertResponseStatusCodeSame(401);
    }

    public function testIndex(): void
    {
        $agent = AgentFactory::createOne();
        $apiToken = ApiTokenFactory::createOne(['agent' => $agent]);
        $numberOfEntities = 5;
        ClientFactory::createMany($numberOfEntities);

        static::createClient()->request('GET', $this->path, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'x-api-token' => $apiToken->getToken()
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount($numberOfEntities, $this->repository->findAll());
    }

    public function testCreate()
    {
        $dataArray = json_encode([
            'firstName' => 'Testing',
            'lastName' => 'Phase',
            'email' => 'testing.phase@email.com',
            'phoneNumber' => "12345678901011"
        ]);

        static::createClient()->request('POST', $this->path, [
            'body' => $dataArray,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $this->repository->findAll());
        $this->assertJson($dataArray);
    }

    public function testShow()
    {
        $client = ClientFactory::createOne();

        static::createClient()->request('GET', $this->path . '/' . $client->getId(), [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJson(json_encode($client));
    }

    public function testEdit()
    {
        $client = ClientFactory::createOne([
            'firstName' => 'NotTesting',
            'lastName' => 'NotPhase',
            'email' => 'not.testing.phase@email.com',
            'phoneNumber' => "11010987654321"
        ]);

        $dataArray = json_encode([
            'firstName' => 'Testing',
            'lastName' => 'Phase',
            'email' => 'testing.phase@email.com',
            'phoneNumber' => "12345678901011"
        ]);

        static::createClient()->request('PATCH', $this->path . '/' . $client->getId(), [
            'body' => $dataArray,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/merge-patch+json'
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJson($dataArray);
    }

    public function testDelete()
    {
        $client = ClientFactory::createOne();

        $this->assertCount(1, $this->repository->findAll());

        static::createClient()->request('DELETE', $this->path . '/' . $client->getId(), [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertCount(0, $this->repository->findAll());
        $this->assertResponseStatusCodeSame(204);
    }
}
