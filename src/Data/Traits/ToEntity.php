<?php


namespace Miniyus\Mapper\Data\Traits;


use Miniyus\Mapper\Data\Entity;
use Miniyus\Mapper\Mapper;
use Closure;

trait ToEntity
{
    /**
     * @param string|null $entity
     * @param Closure|callable|string|null $callback
     * @return Entity|null
     */
    public function toEntity(?string $entity = null, $callback = null): ?Entity
    {
        return Mapper::mappingDto($this, $entity, $callback);
    }
}
