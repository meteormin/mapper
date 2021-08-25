<?php


namespace Miniyus\Mapper\Data\Traits;


use JsonMapper_Exception;
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
     * @throws JsonMapper_Exception
     */
    public function toEntities(?string $entity = null, $callback = null): Entities
    {
        $results = Mapper::newInstance()->mapList($this, $entity, $callback);
        if ($results instanceof Entities) {
            return $results;
        }

        return Entities::newInstance($results);
    }
}
