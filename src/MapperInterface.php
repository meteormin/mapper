<?php

namespace Miniyus\Mapper;

use Miniyus\Mapper\Data\Dto;
use Miniyus\Mapper\Data\Entity;
use Closure;
use Illuminate\Support\Collection;
use Miniyus\Mapper\Maps\MapInterface;

interface MapperInterface
{
    /**
     * @param Collection|Entity $entity
     * @param string|null $dto
     * @param Closure|string|null $callback
     * @return Collection|Dto
     */
    public static function mappingEntity($entity, string $dto = null, $callback = null);

    /**
     * @param Collection|Dto $dto
     * @param string|null $entity
     * @param Closure|string|null $callback
     * @return Collection|Entity
     */
    public static function mappingDto($dto, string $entity = null, $callback = null);

    /**
     * get map
     *
     * @return MapInterface
     */
    public function map(): MapInterface;

    /**
     * get dto
     *
     * @return Dto|Dto[]
     */
    public function getDtoObject();

    /**
     * get entity
     *
     * @return Entity|Entity[]
     */
    public function getEntity();

    /**
     * new DataMapper instance
     *
     * @return MapperInterface
     */
    public static function newInstance(): MapperInterface;
}
