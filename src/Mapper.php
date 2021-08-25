<?php

/**
 * 2021.02.18 수정
 * mapperV2 디렉터리 이동
 * app\Mappers -> app\Libraries
 */

/**
 * 2021.02.04 수정
 * MapperConfig라는 클래스를 추가하여 config/DataMapper.php파일을 보다 쉽게 검색할 수 있게 되었습니다.
 */

/**
 * 2021.02.03 수정
 * 기존 배열 반환이였던 부분들 모두 라라벨 컬렉션으로 대체
 */

namespace Miniyus\Mapper;


use InvalidArgumentException;
use JsonSerializable;
use Miniyus\Mapper\Data\Contracts\Mapable;
use Miniyus\Mapper\Data\Dtos;
use Miniyus\Mapper\Data\Entities;
use Miniyus\Mapper\Maps\Map;
use Miniyus\Mapper\Maps\MapInterface;
use Closure;
use Illuminate\Support\Collection;
use JsonMapper_Exception;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Miniyus\Mapper\Data\Entity;
use Miniyus\Mapper\Data\Dto;

class Mapper implements MapperInterface, Arrayable, Jsonable, JsonSerializable
{
    /**
     * dto
     *
     * @var Dto|Dto[]|null
     */
    protected $dto;

    /**
     * @var Entity|Entity[]|null
     */
    protected $entity;

    /**
     * Dto와 Entity사이 변환 로직을 수행하는 클래스
     * @var MapInterface|null
     */
    protected ?MapInterface $map;

    /**
     * Undocumented function
     * @return static
     */
    public static function newInstance(): Mapper
    {
        return new static();
    }

    /**
     * @param Data\Contracts\Mapable $object
     * @param string|null $class
     * @param Closure|callable $callback
     * @return Mapable
     * @throws JsonMapper_Exception
     */
    public function map(Mapable $object, string $class = null, $callback = null): Mapable
    {
        if ($object instanceof Dto) {
            $this->dto = $object;
            if (is_null($class)) {
                ['entity' => $entity, 'map' => $map] = $this->link($object);
            } else {
                // 입력 받은 타겟 클래스가 존재하면, config/mapper를 참조하지 않는다.
                $entity = $class;
                $map = null;
            }

            if (is_null($entity)) {
                throw new InvalidArgumentException(get_class($object) . ": 매칭되는 클래스를 찾을 수 없습니다. 'config/mapper.php' 파일을 확인해주세요.");
            } else {
                $entity = new $entity;
                $map = is_null($map) ? $map : new $map;
            }

            $this->entity = $entity;
            $this->map = $map;

            return $this->toEntity($object, $this->entity, $callback);

        } else if ($object instanceof Entity) {
            $this->entity = $object;
            if (is_null($class)) {
                ['dto' => $dto, 'map' => $map] = $this->link($object);
            } else {
                // 입력 받은 타겟 클래스가 존재하면, config/mapper를 참조하지 않는다.
                $dto = $class;
                $map = null;
            }

            if (is_null($dto)) {
                throw new InvalidArgumentException(get_class($object) . ": 매칭되는 클래스를 찾을 수 없습니다. 'config/mapper.php' 파일을 확인해주세요.");
            } else {
                // 매칭되는 것이 있으면?
                $dto = new $dto;
                $map = is_null($map) ? $map : new $map;
            }

            $this->dto = $dto;
            $this->map = $map;

            return $this->toDto($object, $this->dto, $callback);
        } else {
            return $object->map(new $class, $callback);
        }
    }

    /**
     * @param array|Collection $list
     * @param string|null $class
     * @param Closure|callable|null $callback
     * @return Collection
     * @throws JsonMapper_Exception
     */
    public function mapList($list, string $class = null, $callback = null): Collection
    {
        if (is_array($list)) {
            if (count($list) == 0) {
                return $list;
            }
            $list = collect($list);
        } else if ($list instanceof Collection) {
            if ($list->count() == 0) {
                return $list;
            }
        } else {
            throw new InvalidArgumentException('parameter 1 is not list... to be array|' . Collection::class);
        }

        // 첫번째 객체 기준으로 클래스 타입이 다른 값들 필터링
        $sourceType = get_class($list->first());
        $list = $list->whereInstanceOf($sourceType);

        if ($list->first() instanceof Dto) {
            $rsList = Dtos::newInstance();
        } else if ($list->first() instanceof Entity) {
            $rsList = Entities::newInstance();
        } else {
            $rsList = collect();
        }

        foreach ($list as $obj) {
            $rsList->add($this->map($obj, $class, $callback));
        }

        return $rsList;
    }

    /**
     * config/mapper파일에서 연결된 클래스 가져오기
     * @param Dto|Entity $obj
     *
     * @return array
     *
     */
    protected function link($obj): array
    {
        $map = MapperConfig::findKeyByClass(get_class($obj));

        if (is_null($map)) {
            return [
                'entity' => null,
                'dto' => null,
                'map' => null
            ];
        }

        ['dto' => $dto, 'entity' => $entity] = MapperConfig::findByAttribute($map);

        return [
            'entity' => $entity,
            'dto' => $dto,
            'map' => $map
        ];
    }

    /**
     * map
     *
     * @return mapInterface
     */
    public function getMap(): MapInterface
    {
        return $this->map;
    }

    /**
     * get dto instance
     * [2021.01.27]
     * 추후 의존성 문제가 발생 여지가 있어 dto도 메서드로 가져온다
     *
     * @return Dto|Dto[]
     */
    public function getDtoObject()
    {
        return $this->dto;
    }

    /**
     * @return Entity|Entity[]
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * mapper에 데이터를 dto형식에 맞게 변환
     * Entity -> Dto
     *
     * @param Entity $entity
     * @param Dto|null $dto
     * @param Closure|callable|string|null $callback
     * @return  Dto
     * @throws JsonMapper_Exception
     * @version 2.5.8 callable 검사와 string 검사 순서 변경
     */
    protected function toDto(Entity $entity, ?Dto $dto, $callback = null): Dto
    {
        if (is_null($entity->toArray())) {
            return $dto;
        }

        if (!is_null($dto) && !is_null($callback)) {
            if (is_callable($callback)) {
                $result = $callback($entity, $dto);
                if (!($dto instanceof Dto) || is_array($result)) {
                    $dto->map($result);
                }
            } else if (class_exists($callback)) {
                /** @var Map $map */
                $map = new $callback;
                if ($map instanceof Map) {
                    $dto = $map->entityToDto($entity, $dto);
                } else {
                    throw new InvalidArgumentException(get_class($map) . ': 콜백 클래스는 Map 클래스를 상속받은 클래스이여야 합니다.');
                }
            } else {
                throw new InvalidArgumentException(get_class($entity) . ': Dto변환 실패 $callback파라미터가 올바르지 않습니다.');
            }
        } else if (!is_null($this->map)) {
            $dto = $this->map->entityToDto($entity, $dto);
        } else if (!is_null($dto)) {
            $dto->map($entity);
        } else {
            throw new InvalidArgumentException(get_class($entity) . ': Dto변환 실패 Dto객체가 null입니다.');
        }

        return $dto;
    }

    /**
     * Dto -> Entity
     * @param Dto $dto
     * @param Entity|null $entity
     * @param Closure|callable|string|null $callback
     * @return Entity
     * @throws JsonMapper_Exception
     * @version 2.5.8 callable 검사와 string 검사 순서 변경
     */
    protected function toEntity(Dto $dto, ?Entity $entity, $callback = null): Entity
    {
        // check empty
        if (is_null($dto->toArray())) {
            return $entity;
        }

        if (!is_null($entity) && !is_null($callback)) {
            if (is_callable($callback)) {
                $result = $callback($dto, $entity);
                if (!($entity instanceof Entity) || is_array($result)) {
                    $entity->map($result);
                }
            } else if (is_string($callback)) {
                /** @var Map $map */
                $map = new $callback;
                $entity = $map->dtoToEntity($dto, $entity);
            }
        } else if (!is_null($this->map)) {
            $entity = $this->map->dtoToEntity($dto, $entity);
        } else if (!is_null($entity)) {
            $entity->map($dto);
        } else {
            throw new InvalidArgumentException(get_class($entity) . ': Entity변환 실패 Entity객체가 null입니다.');
        }

        return $entity;
    }

    /**
     * 디버깅용
     *
     * @return array
     */
    public function toArray(): array
    {
        if (is_array($this->dto) || is_array($this->entity)) {
            $dtos = [];
            foreach ($this->dto as $dto) {
                $dtos[] = $dto->toArray();
            }

            $entities = [];
            foreach ($this->entity as $entity) {
                $entities[] = $entity->toArray();
            }

            return [
                'DataTransferObjects' => $dtos,
                'Entities' => $entities
            ];
        }

        return [
            'DataTransferObject' => $this->dto->toArray(),
            'Entity' => $this->entity->toArray()
        ];
    }

    /**
     * API 응답 디버깅용
     *
     * @param integer $options
     * @return string
     */
    public function toJson($options = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this, $options);
    }

    /**
     * @return array|array[]
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
