<?php

namespace App\Tests\Controller;

use App\Entity\Notification;
use App\Tests\Factory\AgentFactory;
use App\Tests\Factory\ApiTokenFactory;
use App\Tests\Factory\ClientFactory;
use App\Tests\Factory\NotificationFactory;

class NotificationControllerTest extends BaseTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->path = '/api/notifications';
        $this->repository = $this->entityManager->getRepository(Notification::class);
        $this->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'x-api-token' => ApiTokenFactory::createOne(['agent' => AgentFactory::createOne()])->getToken()
        ];
    }

    public function testIndexUnauthorized(): void
    {
        static::createClient()->request('GET', $this->path);
        $this->assertResponseStatusCodeSame(401);
    }

    public function testIndex(): void
    {
        $numberOfEntities = 25;

        NotificationFactory::createMany($numberOfEntities, function () {
            return [
                'clientId' => ClientFactory::createOne()
            ];
        });

        static::createClient()->request('GET', $this->path, [
            'headers' => $this->headers
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount($numberOfEntities, $this->repository->findAll());
    }

    public function testCreateUnauthorized(): void
    {
        static::createClient()->request('POST', $this->path);
        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateEmail()
    {
        $client = ClientFactory::createOne();

        $notificationDataArray = json_encode([
            'clientId' => 'api/clients/' . $client->getId(),
            'channel' => 'email',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                aliquip ex ea commodo consequat.'
        ]);

        static::createClient()->request('POST', $this->path, [
            'body' => $notificationDataArray,
            'headers' => $this->headers
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $this->repository->findAll());
        $this->assertJson($notificationDataArray);
    }

    public function testCreateSMS()
    {
        $client = ClientFactory::createOne();

        $notificationDataArray = json_encode([
            'clientId' => 'api/clients/' . $client->getId(),
            'channel' => 'sms',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                labore et dolores magna aliqua.' //precisely 140 characters long
        ]);

        static::createClient()->request('POST', $this->path, [
            'body' => $notificationDataArray,
            'headers' => $this->headers
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $this->repository->findAll());
        $this->assertJson($notificationDataArray);
    }

    public function testCreateSMSWithLongContent()
    {
        $client = ClientFactory::createOne();

        $notificationDataArray = json_encode([
            'clientId' => 'api/clients/' . $client->getId(),
            'channel' => 'sms',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                aliquip ex ea commodo consequat.'
        ]);

        static::createClient()->request('POST', $this->path, [
            'body' => $notificationDataArray,
            'headers' => $this->headers
        ]);

        $this->assertResponseIsUnprocessable();
        $this->assertResponseStatusCodeSame(422);
        $this->assertCount(0, $this->repository->findAll());
    }

    public function testShowUnauthorized(): void
    {
        $notification = NotificationFactory::createOne(['clientId' => ClientFactory::createOne()]);

        static::createClient()->request('GET', $this->path . '/' . $notification->getId());
        $this->assertResponseStatusCodeSame(401);
    }

    public function testShow()
    {
        $notification = NotificationFactory::createOne(['clientId' => ClientFactory::createOne()]);

        static::createClient()->request('GET', $this->path . '/' . $notification->getId(), [
            'headers' => $this->headers
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJson(json_encode($notification));
    }
}
