<?php


namespace Miniyus\Mapper\Data\Traits;

use Miniyus\Mapper\Data\Dto;
use Miniyus\Mapper\Data\Dtos;
use Miniyus\Mapper\Facades\Mapper;

trait ToDtos
{
    /**
     * Dtos 객체로 변환
     * @param string|null $dto
     * @param callable|Closure|string|null $callback
     * @return Dtos|Dto[]
     */
    public function toDtos(?string $dto = null, $callback = null): Dtos
    {
        $results = Mapper::mapList($this, $dto, $callback);
        if ($results instanceof Dtos) {
            return $results;
        }

        return Dtos::newInstance($results);
    }
}
