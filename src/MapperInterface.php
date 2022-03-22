<?php

namespace Miniyus\Mapper;

use Miniyus\Mapper\Data\Contracts\Mapable;
use Miniyus\Mapper\Data\Dto;
use Miniyus\Mapper\Data\Entity;
use Closure;
use Illuminate\Support\Collection;
use Miniyus\Mapper\Maps\MapInterface;

interface MapperInterface
{
    /**
     * @param Mapable $object
     * @param string|null $class
     * @param Closure|callable|null $callback
     * @return Mapable
     */
    public function map(Mapable $object, string $class = null, $callback = null): Mapable;

    /**
     *
     * @param array|Collection<Mapable> $list
     * @param string|null $class
     * @param Closure|callable|null $callback
     * @return Collection
     */
    public function mapList($list, string $class = null, $callback = null): Collection;

    /**
     * get map
     *
     * @return MapInterface
     */
    public function getMap(): MapInterface;

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
     * @param array $config
     * @return MapperInterface
     */
    public static function newInstance(array $config): MapperInterface;
}
