<?php


namespace Miniyus\Mapper\Data\Traits;

use Miniyus\Mapper\Exceptions\DtoErrorException;
use Miniyus\Mapper\Data\Entity;
use Closure;

/**
 * Trait ReadOnlyDto
 * @package App\Libraries\Data\Traits
 */
trait ReadOnlyDto
{
    /**
     * @param string|null $entity
     * @param Closure|string|null $callback
     * @return Entity
     * @throws DtoErrorException
     */
    public function toEntity(?string $entity = null, $callback = null): Entity
    {
        throw new DtoErrorException('뷰 테이블 Dto는 Entity로 변환이 불가능합니다.', 99);
    }
}
