<?php

namespace Miniyus\Mapper;

use Miniyus\Mapper\Utils\Configure;

class MapperFactory
{
    public static function make(): Mapper
    {
        $config = new Configure(__DIR__ . '/../config', 'mapper');

        return Mapper::newInstance($config->all());
    }
}