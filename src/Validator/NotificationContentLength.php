<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class NotificationContentLength extends Constraint
{
    public $message = 'The length of the content field is too long for SMS. It should not exceed 140 characters.';

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}