<?php

namespace Miniyus\Mapper\Data;

use Miniyus\Mapper\Data\Contracts\Mapable;
use Miniyus\Mapper\Data\Traits\ToDto;
use Miniyus\Mapper\Mapper;
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
    use Transformation;
    use ToDto;

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
     * @param array|Arrayable|null $params
     * @return $this
     * @throws JsonMapper_Exception
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
        if ($model instanceof Arrayable) {
            $model->fill($this->toArray(true));
            return $model;
        } elseif (is_null($model)) {
            throw new EntityErrorException('model is null');
        } else {
            throw new EntityErrorException(get_class($model) . '모델을 매핑할 수 없습니다.');
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
     * @param array|Arrayable $data
     * @param Closure|callable|null $callback
     * @return $this|Arrayable
     * @throws JsonMapper_Exception
     */
    public function map($data, $callback = null): Entity
    {
        return DataMapper::map($data, $this, $callback);
    }

    /**
     * @param $data
     * @param Closure|callable|null $callback
     * @return Entities|$this[]
     * @throws JsonMapper_Exception
     */
    public function mapList($data, $callback = null): Entities
    {
        return Entities::newInstance(DataMapper::mapList($data, $this, $callback)->all());
    }
}
