<?php

namespace Miniyus\Mapper\Facades;

use Illuminate\Support\Facades\Facade;
use Miniyus\Mapper\Data\Contracts\Mapable;

/**
 * Class Mapper
 * @package Miniyus\Mapper\Facades
 * @author Yoo Seongmin <miniyu97@iokcom.com>
 * @method static map(Mapable $object, string $class = null, $callback = null)
 * @method static mapList($list, string $class = null, $callback = null)
 * @method static getMap()
 * @method static getDtoObject()
 * @method static getEntity()
 * @method static toArray()
 * @method static toJson()
 * @method static jsonSerialize()
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