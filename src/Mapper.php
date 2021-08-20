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
use Miniyus\Mapper\Data\Traits\ToDto;
use Miniyus\Mapper\Data\Traits\ToDtos;
use Miniyus\Mapper\Data\Traits\ToEntities;
use Miniyus\Mapper\Data\Traits\ToEntity;
use Miniyus\Mapper\Maps\Map;
use Miniyus\Mapper\Maps\MapInterface;
use Closure;
use Illuminate\Support\Collection;
use JsonMapper_Exception;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Miniyus\Mapper\Data\Entity;
use Miniyus\Mapper\Data\Dto;

class Mapper implements MapperInterface, Arrayable, Jsonable
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
     * 생성 시 받은 매개변수의 type에 따라 매핑 해준다.
     * mapper는 Service 계층에서 사용하며,
     * Service는 input,return을 DTO객체로 사용한다.
     * repository 계층은 항상 input과 return을 Entity로 사용한다.
     * 그 대신 repository의 로직을 간소화(DB관련 데이터 처리만)
     *
     * @param Entity|Dto|null $parameter
     */
    public function __construct($parameter = null)
    {
        if (!is_null($parameter)) {
            if ($parameter instanceof Entity) {
                $this->mappingEntity($parameter);
                return;
            }

            if ($parameter instanceof Dto) {
                $this->mappingDto($parameter);
                return;
            }

            throw new InvalidArgumentException('Possible parameters: Entity|Dto');
        }
    }

    /**
     * Undocumented function
     * @param Entity|Dto|null $parameter
     * @return $this
     */
    public static function newInstance($parameter = null): Mapper
    {
        return new static($parameter);
    }

    /**
     * Entity를 출력 DTO로 변환하기 위해 mapping
     *
     * @param ToDto|ToDtos|Collection|Arrayable $entity
     * @param string|null $dto
     * @param Closure|string|null $callback
     * @return  Collection|Dto|null     [return description]
     *
     */
    public static function mappingEntity($entity, string $dto = null, $callback = null)
    {
        $instance = self::newInstance();
        $method = null;

        if (is_array($entity)) {
            $entity = collect($entity);
        }

        if ($entity instanceof Collection) {
            if ($entity->count() == 0) {
                return $entity;
            }

            // 첫번째 객체 기준으로 클래스 타입이 다른 값들 필터링
            $entity = $entity->whereInstanceOf(get_class($entity->first()));

            // config/mapper파일의 저장된 정보를 기준으로 연결되어 있는 클래스들 생성
            $instance->entity = $entity;

            ['dto' => $instance->dto, 'map' => $instance->map] = $instance->link($entity->first());

            $method = 'toDtos';
        }

        if ($entity instanceof Entity) {
            $instance->entity = $entity;

            ['dto' => $instance->dto, 'map' => $instance->map] = $instance->link($entity);

            $method = 'toDto';
        }

        if (is_null($instance->dto) || is_null($instance->entity)) {
            if (is_null($dto)) {
                throw new InvalidArgumentException(get_class($instance->entity) . ": 매칭되는 클래스를 찾을 수 없습니다. 'config/mapper.php' 파일을 확인해주세요.");
            }
            $entity = $instance->entity;
            $dto = new $dto;
        } else {
            $entity = $instance->entity;
            $dto = $instance->dto;
        }

        if (method_exists($instance, $method)) {
            return $instance->$method($entity, $dto, $callback);
        }

        return null;
    }

    /**
     * Dto 데이터를 model 형식에 맞게 변환
     * Dto -> Entity
     *
     * @param ToEntity|ToEntities|Collection $dto [$dto description]
     * @param string|null $entity
     * @param Closure|string|null $callback
     * @return Collection|Entity|null      [return description]
     *
     * @throws InvalidArgumentException
     */
    public static function mappingDto($dto, string $entity = null, $callback = null)
    {
        $instance = static::newInstance();
        $method = null;

        if (is_array($dto)) {
            $dto = collect($dto);
        }

        if ($dto instanceof Collection) {
            if ($dto->count() == 0) {
                return $dto;
            }

            // 첫번째 객체 기준으로 클래스 타입이 다른 값들 필터링
            $dto = $dto->whereInstanceOf(get_class($dto->first()));

            // config/mapper파일의 저장된 정보를 기준으로 연결되어 있는 클래스들 생성
            $instance->dto = $dto;
            ['entity' => $instance->entity, 'map' => $instance->map] = $instance->link($dto->first());

            $method = 'toEntities';
        }

        if ($dto instanceof Dto) {
            $instance->dto = $dto;
            ['entity' => $instance->entity, 'map' => $instance->map] = $instance->link($dto);

            $method = 'toEntity';
        }

        if (is_null($instance->dto) || is_null($instance->entity)) {
            if (is_null($entity)) {
                throw new InvalidArgumentException(get_class($instance->dto) . ": 매칭되는 클래스를 찾을 수 없습니다. 'config/mapper.php' 파일을 확인해주세요.");
            }
            $dto = $instance->dto;
            $entity = new $entity;
        } else {
            $dto = $instance->dto;
            $entity = $instance->entity;
        }

        $result = null;

        if (method_exists($instance, $method)) {
            $result = $instance->$method($dto, $entity, $callback);
        }

        return $result;
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
            'entity' => new $entity,
            'dto' => new $dto,
            'map' => new $map
        ];
    }

    /**
     * map
     *
     * @return mapInterface
     */
    public function map(): MapInterface
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
     * @return  Dto  [return description]
     * @throws JsonMapper_Exception
     *  @version 2.5.8 callable 검사와 string 검사 순서 변경
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
     * Undocumented function
     *
     * @param array|Collection $entities
     * @param Dto|null $dto
     * @param Closure|callable|string|null $callback
     * @return Collection
     * @throws JsonMapper_Exception
     */
    protected function toDtos(Collection $entities, Dto $dto, $callback = null): Collection
    {
        $dtos = [];
        if (is_array($entities) || $entities instanceof \Illuminate\Support\Collection) {

            foreach ($entities as $entity) {
                $dtos[] = $this->toDto($entity, $dto->newInstance(), $callback);
            }
        }
        return $this->dto = collect($dtos);
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
     * Undocumented function
     *
     * @param Collection $dtos
     * @param Entity|null $entity
     * @param Closure|callable|null $callback
     * @return Collection
     * @throws JsonMapper_Exception
     */
    protected function toEntities(Collection $dtos, Entity $entity, Closure $callback = null): Collection
    {
        $entities = [];
        if (is_array($dtos) || $dtos instanceof \Illuminate\Support\Collection) {
            foreach ($dtos as $dto) {
                $entities[] = $this->toEntity($dto, $entity->newInstance(), $callback);
            }
        }
        return $this->entity = collect($entities);
    }

    /**
     * 디버깅용
     *
     * @return array  [return description]
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
        return json_encode($this->toArray(), $options);
    }
}
