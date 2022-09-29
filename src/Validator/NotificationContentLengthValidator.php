<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

#[\Attribute]
class NotificationContentLengthValidator extends ConstraintValidator
{
    public function validate($object, Constraint $constraint)
    {
        if (!$constraint instanceof NotificationContentLength) {
            throw new UnexpectedTypeException($constraint, NotificationContentLength::class);
        }

        $content = $object->getContent();

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $content || '' === $content) {
            return;
        }

        if (!is_string($content)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($content, 'string');
        }

        if ($object->getChannel() === 'sms' && mb_strlen($content) > 140) {
            // the argument must be a string or an object implementing __toString()
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}