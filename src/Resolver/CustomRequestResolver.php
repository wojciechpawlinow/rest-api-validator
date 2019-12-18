<?php
declare(strict_types=1);

namespace Pawly\RestApiValidator\Resolver;

use Generator;
use Pawly\RestApiValidator\Exception\RequestValidationException;
use Pawly\RestApiValidator\Request\CustomRequestInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * This class overrides default ArgumentValueResolver
 * and provides extra validation for CustomRequestInterface objects.
 */
class CustomRequestResolver implements ArgumentValueResolverInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if ($argument->getType() === ''
            || $argument->getType() === null
            || class_exists($argument->getType()) === false
        ) {
            return false;
        }

        try {
            $reflection = new ReflectionClass($argument->getType());
        } catch (ReflectionException $e) {
            throw new $e();
        }

        return $reflection->implementsInterface(CustomRequestInterface::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): ?Generator
    {
        $class = $argument->getType();
        $customRequest = new $class($request);

        $errors = $this->validator->validate(
            $customRequest->getValidatableData(),
            $customRequest->getValidationRules()
        );

        $unexpectedParams = $customRequest->getUnexpectedParams();

        if (0 < count($unexpectedParams)) {
            foreach ($unexpectedParams as $param) {
                $errors->add(new ConstraintViolation(
                    'Unexpected field in request',
                    '',
                    [],
                    $customRequest,
                    $param,
                    'Unexpected field in request'
                ));
            }
        }

        if (0 < count($errors)) {
            throw new RequestValidationException($errors);
        }

        yield $customRequest;
    }
}
