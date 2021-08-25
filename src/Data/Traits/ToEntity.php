<?php


namespace Miniyus\Mapper\Data\Traits;


use JsonMapper_Exception;
use Miniyus\Mapper\Data\Contracts\Mapable;
use Miniyus\Mapper\Data\Entity;
use Miniyus\Mapper\Mapper;
use Closure;

trait ToEntity
{
    /**
     * @param string|null $entity
     * @param Closure|callable|string|null $callback
     * @return Entity|Mapable|null
     * @throws JsonMapper_Exception
     */
    public function toEntity(?string $entity = null, $callback = null): ?Entity
    {
        return Mapper::newInstance()->map($this, $entity, $callback);
    }
}
