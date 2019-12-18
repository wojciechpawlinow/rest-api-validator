<?php
declare(strict_types=1);

namespace Pawly\RestApiValidator\Exception;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RequestValidationException extends UnprocessableEntityHttpException
{
    public const MESSAGE = 'Request validation error';

    private ConstraintViolationListInterface $errors;

    public function __construct(ConstraintViolationListInterface $errors)
    {
        parent::__construct(self::MESSAGE, null);

        $this->errors = $errors;
    }

    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
