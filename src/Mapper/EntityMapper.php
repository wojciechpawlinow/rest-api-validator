<?php
declare(strict_types=1);

namespace Pawly\RestApiValidator\Mapper;

use JsonSerializable;

class EntityMapper implements EntityMapperInterface
{
    /**
     * @param JsonSerializable[] $items
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     */
    public static function fromCollection(array $items): array
    {
        $results = [];

        foreach ($items as $item) {
            $results[] = static::fromModel($item);
        }

        return $results;
    }

    /**
     * @param JsonSerializable $item
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     */
    public static function fromModel(JsonSerializable $item): array
    {
        return $item->jsonSerialize();
    }
}
