<?php
declare(strict_types=1);

namespace Pawly\RestApiValidator\Request;

use Symfony\Component\Validator\Constraint;

interface CustomRequestInterface
{
    /** @return Constraint|Constraint[] */
    public function getValidationRules();

    public function getValidatableData(): array;

    public function getUnexpectedParams(): array;

}
