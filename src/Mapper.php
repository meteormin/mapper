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
     * @var MapperConfig
     */
    protected MapperConfig $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = new MapperConfig($config);
    }

    /**
     * Undocumented function
     * @return static
     */
    public static function newInstance(array $config): Mapper
    {
        return new static($config);
    }

    /**
     * @return MapperConfig
     */
    public function config(): MapperConfig
    {
        return $this->config;
    }

    /**
     * @param Data\Contracts\Mapable $object
     * @param string|null $class
     * @param null $callback
     * @return Mapable
     * @throws Exceptions\DtoErrorException
     * @throws Exceptions\EntityErrorException
     * @throws JsonMapper_Exception
     */
    public function map(Mapable $object, string $class = null, $callback = null): Mapable
    {
        if ($object instanceof Dto) {
            $this->dto = $object;

            $entity = $class;
            $map = null;

            // 입력 받은 타겟 클래스가 존재하지 않을 경우 config/mapper.php 에서 참조하여 객체를 연결 해준다.
            if (is_null($class)) {
                ['entity' => $entity, 'map' => $map] = $this->link($object);
            }

            if (is_null($entity)) {
                throw new InvalidArgumentException(get_class($object) . ": 매칭되는 클래스를 찾을 수 없습니다. 'config/mapper.php' 파일을 확인해주세요.");
            }

            $this->map = is_null($map) ? null : $this->makeMapClass($map, get_class($object), $entity);
            $this->entity = new $entity;

            return $this->toEntity($object, $this->entity, $callback);

        } else if ($object instanceof Entity) {
            $this->entity = $object;

            $dto = $class;
            $map = null;

            // 입력 받은 타겟 클래스가 존재하지 않을 경우 config/mapper.php 에서 참조하여 객체를 연결 해준다.
            if (is_null($class)) {
                ['dto' => $dto, 'map' => $map] = $this->link($object);
            }

            if (is_null($dto)) {
                throw new InvalidArgumentException(get_class($object) . ": 매칭되는 클래스를 찾을 수 없습니다. 'config/mapper.php' 파일을 확인해주세요.");
            }

            $this->map = is_null($map) ? null : $this->makeMapClass($map, $dto, get_class($object));
            $this->dto = new $dto;

            return $this->toDto($object, $this->dto, $callback);
        }

        return $object->map(new $class, $callback);
    }

    /**
     * @param string $mapClass
     * @param string $dtoClass
     * @param string $entityClass
     * @return Map|null
     */
    protected function makeMapClass(string $mapClass, string $dtoClass, string $entityClass): ?Map
    {
        if (empty($mapClass) || empty($dtoClass) || empty($entityClass)) {
            return null;
        }

        return new $mapClass($dtoClass, $entityClass);
    }

    /**
     * @param array|Collection $list
     * @param string|null $class
     * @param Closure|callable|null $callback
     * @return Collection
     * @throws Exceptions\DtoErrorException
     * @throws Exceptions\EntityErrorException
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
        $map = $this->config->findMapByClass(get_class($obj));

        if (is_null($map)) {
            return [
                'entity' => null,
                'dto' => null,
                'map' => null
            ];
        }

        ['dto' => $dto, 'entity' => $entity] = $this->config->findByMap($map);

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
     * @throws JsonMapper_Exception|Exceptions\DtoErrorException
     * @version 2.5.8 callable 검사와 string 검사 순서 변경
     */
    protected function toDto(Entity $entity, ?Dto $dto, $callback = null): Dto
    {
        if (is_null($entity->toArray())) {
            return $dto;
        }

        if (is_null($dto)) {
            throw new InvalidArgumentException(get_class($entity) . ': Dto변환 실패 Dto객체가 null입니다.');
        }

        if (!is_null($callback)) {

            if (is_callable($callback)) {

                $result = $callback($entity, $dto);

                if ($result instanceof Dto) {

                    return $result;
                }

                return $dto->map($result);

            } else if (class_exists($callback) && (($map = new $callback) instanceof Map)) {

                return $map->entityToDto($entity, $dto);
            }

            throw new InvalidArgumentException(get_class($entity) . ": Dto변환 실패 \$callback('{$callback}')파라미터가 올바르지 않습니다.");

        } else if (!is_null($this->map)) {

            return $this->map->entityToDto($entity, $dto);
        } else {

            return $dto->map($entity);
        }
    }

    /**
     * Dto -> Entity
     * @param Dto $dto
     * @param Entity|null $entity
     * @param Closure|callable|string|null $callback
     * @return Entity
     * @throws JsonMapper_Exception|Exceptions\EntityErrorException
     * @version 2.5.8 callable 검사와 string 검사 순서 변경
     */
    protected function toEntity(Dto $dto, ?Entity $entity, $callback = null): Entity
    {
        // check empty
        if (is_null($dto->toArray())) {

            return $entity;
        }

        if (is_null($entity)) {

            throw new InvalidArgumentException(get_class($dto) . ': Entity변환 실패 Entity객체가 null입니다.');
        }

        if (!is_null($callback)) {

            if (is_callable($callback)) {

                $result = $callback($dto, $entity);

                if ($result instanceof Entity) {
                    return $result;
                }

                return $entity->map($result);

            } else if (class_exists($callback) && (($map = new $callback) instanceof Map)) {

                return $map->dtoToEntity($dto, $entity);
            }

            throw new InvalidArgumentException(get_class($dto) . ": Entity변환 실패 \$callback('{$callback}')파라미터가 올바르지 않습니다.");

        } else if (!is_null($this->map)) {

            return $this->map->dtoToEntity($dto, $entity);
        } else {

            return $entity->map($dto);
        }
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
