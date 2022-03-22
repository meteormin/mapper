<?php

namespace Miniyus\Mapper\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Miniyus\Mapper\Data\Contracts\Mapable;
use Miniyus\Mapper\Data\Dto;
use Miniyus\Mapper\Data\Entity;
use Miniyus\Mapper\MapperConfig;
use Miniyus\Mapper\Maps\MapInterface;

/**
 * Class Mapper
 * @package Miniyus\Mapper\Facades
 * @author Yoo Seongmin <miniyu97@iokcom.com>
 * @method static MapperConfig config()
 * @method static Mapable map(Mapable $object, string $class = null, $callback = null)
 * @method static Collection mapList($list, string $class = null, $callback = null)
 * @method static MapInterface getMap()
 * @method static Dto|Dto[] getDtoObject()
 * @method static Entity|Entity[] getEntity()
 * @method static array toArray()
 * @method static string toJson()
 * @method static array jsonSerialize()
 *
 * @see \Miniyus\Mapper\Mapper
 */
class Mapper extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'mapper';
    }
}