<?php


namespace Miniyus\Mapper\Data\Traits;


use JsonMapper_Exception;
use Miniyus\Mapper\Data\Contracts\Mapable;
use Miniyus\Mapper\Data\Dto;
use Miniyus\Mapper\Exceptions\DtoErrorException;
use Miniyus\Mapper\Exceptions\EntityErrorException;
use Miniyus\Mapper\Mapper;
use Closure;

trait ToDto
{
    /**
     * @param string|null $dto
     * @param Closure|callable|string|null $callback
     * @return Dto|Mapable|null
     * @throws JsonMapper_Exception
     * @throws DtoErrorException
     * @throws EntityErrorException
     */
    public function toDto(?string $dto = null, $callback = null): ?Dto
    {
        return Mapper::newInstance()->map($this, $dto, $callback);
    }
}
