<?php

namespace App\Message;

final class SendEmailNotification
{
    private string $email;
    private string $content;

    public function __construct(string $email, string $content)
    {
        $this->email = $email;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}