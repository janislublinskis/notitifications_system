<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\Texter;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class SendNotificationService
{
    public function send(Notification $notification)
    {
        //@TODO: Rewrite 'sms' case with valid SMS sending broker

        $transport = Transport::fromDsn($_ENV['MAILER_DSN']);

        switch($notification->getChannel())
        {
            case 'sms':
                $sender = new Texter($transport);
                $notificationClass = 'Symfony\Component\Mime\Email';
                break;
            case 'email':
            default:
                $sender = new Mailer($transport);
            $notificationClass = 'Symfony\Component\Mime\Email';
                break;
        }

        $client = $notification->getClientId();

        $notificationToSend = (new $notificationClass())
            ->from('support@example.com')
            ->to($client->getEmail())
            ->subject('!Notification!')
            ->text($notification->getContent());

        $sender->send($notificationToSend);
    }
}