<?php


namespace Miniyus\Mapper\Data\Traits;

use JsonMapper_Exception;
use Miniyus\Mapper\Data\Dto;
use Miniyus\Mapper\Data\Dtos;
use Miniyus\Mapper\Exceptions\DtoErrorException;
use Miniyus\Mapper\Exceptions\EntityErrorException;
use Miniyus\Mapper\Facades\Mapper;
use Miniyus\Mapper\MapperFactory;

trait ToDtos
{
    /**
     * Dtos 객체로 변환
     * @param string|null $dto
     * @param callable|Closure|string|null $callback
     * @return Dtos|Dto[]
     * @throws JsonMapper_Exception
     * @throws DtoErrorException
     * @throws EntityErrorException
     */
    public function toDtos(?string $dto = null, $callback = null): Dtos
    {
        try {
            $results = Mapper::mapList($this, $dto, $callback);
        } catch (\Throwable$e) {
            $results = MapperFactory::make()->mapList($this, $dto, $callback);
        }

        if ($results instanceof Dtos) {
            return $results;
        }

        return Dtos::newInstance($results);
    }
}
