<?php
declare(strict_types=1);

namespace Pawly\RestApiValidator\Response;

use JsonSerializable;

interface ApiResponseInterface
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
    ): ApiResponse;

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
    ): ApiResponse;
}
