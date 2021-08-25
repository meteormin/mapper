<?php

namespace Miniyus\Mapper\Data;

use Miniyus\Mapper\Exceptions\DtoErrorException;
use Miniyus\Mapper\Data\Contracts\Mapable;
use Miniyus\Mapper\Data\Traits\ToEntity;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonMapper_Exception;
use JsonSerializable;
use Miniyus\Mapper\Data\Traits\Transformation;

abstract class Dto implements Mapable, JsonSerializable
{
    use Transformation;
    use ToEntity;

    /**
     * @throws JsonMapper_Exception
     */
    public function __construct($params = null)
    {
        $this->map($params);
    }

    /**
     * @param $name
     * @param $value
     * @throws DtoErrorException
     */
    public function __set($name, $value)
    {
        throw new DtoErrorException('DataTransferObject can not has dynamic property');
    }

    /**
     * @param array|Arrayable|null $params
     * @return $this
     * @throws JsonMapper_Exception
     */
    public static function newInstance($params = null): Dto
    {
        return new static($params);
    }

    /**
     * @param Arrayable|Mapable|Jsonable|array|object $data
     * @param Closure|callable|null $callback
     * @return $this|Mapable
     * @throws JsonMapper_Exception
     */
    public function map($data, $callback = null): Dto
    {
        return DataMapper::map($data, $this, $callback);
    }

    /**
     * @param $data
     * @param Closure|Mapable|Arrayable|callable|Jsonable|null $callback
     * @return $this[]|Dtos<$this>
     * @throws JsonMapper_Exception
     */
    public function mapList($data, $callback = null): Dtos
    {
        return Dtos::newInstance(DataMapper::mapList($data, $this, $callback)->all());
    }
}
