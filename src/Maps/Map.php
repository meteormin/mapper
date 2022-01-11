<?php

namespace Miniyus\Mapper\Maps;

use Miniyus\Mapper\Data\Dto;
use Miniyus\Mapper\Data\Entity;
use JsonMapper_Exception;
use Miniyus\Mapper\Exceptions\DtoErrorException;
use Miniyus\Mapper\Exceptions\EntityErrorException;

abstract class Map implements MapInterface
{
    /**
     * @var string|null
     */
    protected ?string $dto_class;

    /**
     * @var string|null
     */
    protected ?string $entity_class;

    /**
     * @var string|null
     */
    protected ?string $id;

    /**
     * construct
     * config/mapper.tables에 해당 키로 매칭되는 크래스들을 찾는다.
     * @param string|null $id table name(in config/mapper.maps)
     */
    public function __construct(?string $id = null)
    {
        $this->id = $id;
        $map = get_class($this);

        $this->dto_class = config("mapper.maps.{$map}.dto");
        $this->entity_class = config("mapper.maps.{$map}.entity");
    }

    /**
     * 구현 시, php docblock으로 실제 Entity와 Dto의 타입을 명시해야 IDE의 도움을 받을 수 있음
     * 혹은 instanceof 를 사용할 것
     * @param Dto $dto
     * @param Entity $entity
     * @return Entity|array
     */
    abstract protected function toEntity(Dto $dto, Entity $entity);

    /**
     * 구현 시, php docblock으로 실제 Entity와 Dto의 타입을 명시해야 IDE의 도움을 받을 수 있음
     * 혹은 instanceof 를 사용할 것
     * @param Entity $entity
     * @param Dto $dto
     * @return Dto|array
     */
    abstract protected function toDto(Entity $entity, Dto $dto);

    /**
     * toEntity 실행 전, instanceof로 클래스 매칭이 되어 있는지 체크
     * @param Dto $dto
     * @param Entity $entity
     * @return Entity
     * @throws JsonMapper_Exception
     * @throws EntityErrorException
     */
    public function dtoToEntity(Dto $dto, Entity $entity): Entity
    {
        $dto_class = $this->dto_class;
        $entity_class = $this->entity_class;

        if ($dto instanceof $dto_class && $entity instanceof $entity_class) {
            $result = $this->toEntity($dto, $entity);
            if(is_array($result)){
                $entity->map($result);
            } elseif($result instanceof Entity){
                $entity = $result;
            }
        }

        return $entity;
    }

    /**
     * toDto 실행 전, instanceof로 클래스 매칭이 되어 있는지 체크
     * @param Entity $entity
     * @param Dto $dto
     * @return Dto
     * @throws JsonMapper_Exception
     * @throws DtoErrorException
     */
    public function entityToDto(Entity $entity, Dto $dto): Dto
    {
        $dto_class = $this->dto_class;
        $entity_class = $this->entity_class;

        if ($dto instanceof $dto_class && $entity instanceof $entity_class) {
            $result = $this->toDto($entity, $dto);
            if(is_array($result)) {
                $dto->map($result);
            }else if($result instanceof Dto){
                $dto = $result;
            }
        }

        return $dto;
    }
}
