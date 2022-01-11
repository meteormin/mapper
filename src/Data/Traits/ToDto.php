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
     * 콜백이 있는 경우, 매핑 처리 로직이 콜백함수 내부의 로직으로 대체됩니다.
     * callback 규칙: function({매핑 데이터}, {매핑 객체})
     * @param string|null $dto
     * @param callable|Closure|string|null $callback
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
