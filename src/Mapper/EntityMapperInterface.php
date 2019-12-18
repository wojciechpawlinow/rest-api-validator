<?php
declare(strict_types=1);

namespace Pawly\RestApiValidator\Mapper;

use JsonSerializable;

interface EntityMapperInterface
{
    /**
     * @param JsonSerializable[] $items
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     */
    public static function fromCollection(array $items): array;

    /**
     * @param JsonSerializable $item
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     */
    public static function fromModel(JsonSerializable $item): array;
}
