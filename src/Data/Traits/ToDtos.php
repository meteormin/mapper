<?php


namespace Miniyus\Mapper\Data\Traits;


use Miniyus\Mapper\Data\Dto;
use Miniyus\Mapper\Data\Dtos;
use Miniyus\Mapper\Mapper;
use ArrayAccess;
use Closure;

trait ToDtos
{
    /**
     * Dtos 객체로 변환
     * @param string|null $dto
     * @param Closure|callable|string|null $callback
     * @return Dtos|Dto[]
     */
    public function toDtos(?string $dto = null, $callback = null): Dtos
    {
        /** @var array|ArrayAccess $results */
        $results = Mapper::mappingEntity($this, $dto, $callback);

        return Dtos::newInstance($results);
    }
}
