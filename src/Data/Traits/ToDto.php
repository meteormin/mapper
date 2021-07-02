<?php


namespace Miniyus\Mapper\Data\Traits;


use Miniyus\Mapper\Data\Dto;
use Miniyus\Mapper\Mapper;
use Closure;

trait ToDto
{
    /**
     * @param string|null $dto
     * @param Closure|callable|string|null $callback
     * @return Dto|null
     */
    public function toDto(?string $dto = null, $callback = null): ?Dto
    {
        return Mapper::mappingEntity($this, $dto, $callback);
    }
}
