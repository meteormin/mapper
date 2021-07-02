<?php


namespace Miniyus\Mapper\Data\Traits;


use Miniyus\Mapper\Data\Entities;
use Miniyus\Mapper\Data\Entity;
use Miniyus\Mapper\Mapper;
use ArrayAccess;

trait ToEntities
{
    /**
     * Entities 객체로 변환
     *
     * @param string|null $entity
     * @param Closure|callable|string|null $callback
     * @return Entities|Entity[]
     */
    public function toEntities(?string $entity = null, $callback = null): Entities
    {
        /** @var array|ArrayAccess $results */
        $results = Mapper::mappingDto($this, $entity, $callback);

        return Entities::newInstance($results);
    }
}
