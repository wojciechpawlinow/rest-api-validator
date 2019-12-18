<?php
declare(strict_types=1);

namespace Pawly\RestApiValidator\Response;

use Pawly\RestApiValidator\Mapper\EntityMapper;
use JsonSerializable;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse implements ApiResponseInterface
{
    /**
     * @param JsonSerializable[] $data
     * @param int                $status
     * @param string[string]     $header
     * @param bool               $json
     *
     * @return ApiResponse
     */
    public static function json(
        JsonSerializable $data,
        int $status = ApiResponse::HTTP_OK,
        array $headers = [],
        bool $json = false
    ): ApiResponse {
        return new self(EntityMapper::fromModel($data), $status, $headers, $json);
    }

    /**
     * @param JsonSerializable[] $data
     * @param int|null           $total
     * @param int                $page
     * @param int                $maxPerPage
     * @param int                $status
     * @param string[string]     $headers
     * @param bool               $json
     *
     * @return ApiResponse
     */
    public static function jsonCollection(
        array $data,
        ?int $total = null,
        int $page = 1,
        int $maxPerPage = 30,
        int $status = ApiResponse::HTTP_OK,
        array $headers = [],
        bool $json = false
    ): ApiResponse {
        return new self([
            'data' => [
                'collection' => EntityMapper::fromCollection($data),
            ],
            'total' => $total ?? count($data),
            'page' => $page,
            'totalPages' => ceil($total / $maxPerPage),
        ], $status, $headers, $json);
    }
}
