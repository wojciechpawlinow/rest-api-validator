<?php
declare(strict_types=1);

namespace Pawly\RestApiValidator\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * An abstract class that dynamically matches incoming data
 * with validation constraints
 */
abstract class AbstractCustomRequest implements CustomRequestInterface
{
    protected array $validatableData = [];

    private array $unexpectedParams = [];

    public function __construct(Request $request)
    {
        $this->mapRequestFields($request);
    }

    public function __call(string $method, array $params)
    {
        $var = lcfirst(substr($method, 3));

        if (strncasecmp($method, 'get', 3) === 0) {
            return $this->{$var};
        }

        if (strncasecmp($method, 'set', 3) !== 0) {
            return;
        }

        $this->{$var} = $params[0];
    }

    private function mapRequestFields(Request $request): void
    {
        $params = [];

        if ($request->isMethod('GET')) {
            $data = $request->query->all();

            foreach ($data as $queryParam => $queryParamValue) {
                $params[$queryParam] = json_decode($queryParamValue ?? '', true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    continue;
                }

                $params[$queryParam] = $queryParamValue;
            }
        } else {
            $content = $request->getContent();
            $params = (array) json_decode((string) $content);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new BadRequestHttpException('Invalid JSON');
            }
        }

        $params = array_merge($params, $request->attributes->all());
        $params = array_filter($params,
            fn ($k) => $k !== '_route' && $k !== '_controller' && $k !== '_route_params' && $k !== '_firewall_context',
            ARRAY_FILTER_USE_KEY
        );

        $properties = [];

        foreach ($this as $property => $value) {
            $properties[] = $property;

            if (!isset($params[$property])) {
                continue;
            }

            $this->{$property} = $params[$property];
            $this->validatableData[$property] = $params[$property];
        }

        foreach ($params as $param => $value) {
            if (in_array($param, $properties, true)) {
                continue;
            }

            $this->unexpectedParams[] = $param;
        }
    }

    public function getUnexpectedParams(): array
    {
        return $this->unexpectedParams;
    }

    public function getValidatableData(): array
    {
        return $this->validatableData;
    }
}
