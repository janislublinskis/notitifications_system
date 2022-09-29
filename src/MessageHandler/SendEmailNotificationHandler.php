<?php

namespace App\MessageHandler;

use App\Message\SendEmailNotification;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;

class SendEmailNotificationHandler implements MessageHandlerInterface
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(SendEmailNotification $notificationEmail)
    {
        try {
            $this->mailer->send(
                (new Email())
                ->from('support@example.com')
                ->to($notificationEmail->getEmail())
                ->subject('!Notification!')
                ->text($notificationEmail->getContent())
            );
        } catch (TransportExceptionInterface $e) {
        }
    }
}