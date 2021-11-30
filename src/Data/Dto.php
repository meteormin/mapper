<?php

namespace Miniyus\Mapper\Data;

use ArrayAccess;
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
     * @param array|Arrayable|Mapable|ArrayAccess|object|null $params
     * @throws JsonMapper_Exception
     * @throws DtoErrorException
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
     * @return static
     * @throws JsonMapper_Exception|DtoErrorException
     */
    public static function newInstance($params = null): Dto
    {
        return new static($params);
    }

    /**
     * @param Arrayable|Mapable|Jsonable|array|object $data
     * @param Closure|callable|null $callback
     * @return $this
     * @throws JsonMapper_Exception|DtoErrorException
     */
    public function map($data, $callback = null): Dto
    {
        $dto = DataMapper::map($data, $this, $callback);
        if ($dto !== $this) {
            throw new DtoErrorException('mapped result is Invalid in map()');
        }

        return $this;
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
