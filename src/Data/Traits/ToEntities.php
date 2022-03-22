<?php


namespace Miniyus\Mapper\Data\Traits;

use JsonMapper_Exception;
use Miniyus\Mapper\Data\Entities;
use Miniyus\Mapper\Data\Entity;
use Miniyus\Mapper\Exceptions\DtoErrorException;
use Miniyus\Mapper\Exceptions\EntityErrorException;
use Miniyus\Mapper\Facades\Mapper;
use Miniyus\Mapper\MapperFactory;
use Throwable;

trait ToEntities
{
    /**
     * Entities 객체로 변환
     *
     * @param string|null $entity
     * @param callable|Closure|string|null $callback
     * @return Entities|Entity[]
     * @throws JsonMapper_Exception
     * @throws DtoErrorException
     * @throws EntityErrorException
     */
    public function toEntities(?string $entity = null, $callback = null): Entities
    {
        try {
            $results = Mapper::mapList($this, $entity, $callback);
        } catch (Throwable $e) {
            $results = MapperFactory::make()->mapList($this, $entity, $callback);
        }

        if ($results instanceof Entities) {
            return $results;
        }

        return Entities::newInstance($results);
    }
}
