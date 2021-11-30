<?php

namespace Miniyus\Mapper\Data;


use Miniyus\Mapper\Data\Contracts\Mapable;
use Miniyus\Mapper\Data\Traits\ToDto;
use Closure;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;
use Miniyus\Mapper\Exceptions\EntityErrorException;
use Illuminate\Contracts\Support\Arrayable;
use JsonMapper_Exception;
use JsonSerializable;
use Miniyus\Mapper\Data\Traits\Transformation;

/**
 * Entity는 DB의 필드 스키마정보를 가지는 객체이다
 * getter는 null 허용 필드가 아닌데 null을 리턴할 경우 에러발생 하도록 처리 할 것
 */
abstract class Entity implements Mapable, JsonSerializable
{
    use Transformation {
        toArray as protected TraitToArray;
    }

    use ToDto;

    /**
     * @param array|object|null $params
     * @throws JsonMapper_Exception|EntityErrorException
     */
    public function __construct($params = null)
    {
        $this->map($params);
    }

    /**
     * @param $name
     * @param $value
     * @throws EntityErrorException
     */
    public function __set($name, $value)
    {
        throw new EntityErrorException('Entity can not has dynamic property');
    }

    /**
     * 모델 객체와 강하게 결합하기 위해 작성
     * \App\Models\{모델명}::class
     * @return string
     */
    abstract protected function getIdentifier(): string;

    /**
     * @param array|object|null $params
     * @return static
     * @throws JsonMapper_Exception|EntityErrorException
     */
    public static function newInstance($params = null): Entity
    {
        return new static($params);
    }

    /**
     * @return Model
     * @throws EntityErrorException
     */
    public function toModel(): Model
    {
        $model = $this->model();
        if ($model instanceof Model) {
            return $model->fill($this->toArray());
        } elseif (is_null($model)) {
            throw new EntityErrorException('model is null');
        } else {
            throw new EntityErrorException(get_class($model) . 'is not model');
        }
    }

    /**
     * 모델 객체 가져오기
     *
     * @return Model|null
     */
    protected function model(): ?Model
    {
        $model = $this->getIdentifier();

        if (is_null($model)) {
            return null;
        }

        return new $model;
    }

    /**
     * @param Arrayable|Mapable|Jsonable|array|object $data
     * @param Closure|callable|null $callback
     * @return $this
     * @throws JsonMapper_Exception|EntityErrorException
     */
    public function map($data, $callback = null): Entity
    {
        $entity = DataMapper::map($data, $this, $callback);

        if ($entity !== $this) {
            throw new EntityErrorException('mapped result is Invalid in map()');
        }

        return $this;
    }

    /**
     * @param $data
     * @param Closure|callable|null $callback
     * @return Entities<$this>|$this[]
     * @throws JsonMapper_Exception
     */
    public function mapList($data, $callback = null): Entities
    {
        return Entities::newInstance(DataMapper::mapList($data, $this, $callback)->all());
    }

    /**
     * Entity는 항상 null 허용해야 한다.
     * @param bool $allowNull
     * @return array|null
     */
    public function toArray(bool $allowNull = null): ?array
    {
        return $this->TraitToArray(true);
    }
}
