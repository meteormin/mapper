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
     * @throws \JsonMapper_Exception
     */
    public function toDtos(?string $dto = null, $callback = null): Dtos
    {
        $results = Mapper::newInstance()->mapList($this, $dto, $callback);
        if ($results instanceof Dtos) {
            return $results;
        }

        return Dtos::newInstance($results);
    }
}
