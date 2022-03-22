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
    protected ?string $dtoClass;

    /**
     * @var string|null
     */
    protected ?string $entityClass;

    /**
     * @param string $dtoClass
     * @param string $entityClass
     */
    public function __construct(string $dtoClass, string $entityClass)
    {
        $this->dtoClass = $dtoClass;
        $this->entityClass = $entityClass;
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
        $dtoClass = $this->dtoClass;
        $entityClass = $this->entityClass;

        if ($dto instanceof $dtoClass && $entity instanceof $entityClass) {

            $result = $this->toEntity($dto, $entity);

            if (is_array($result)) {

                $entity->map($result);

            } else if ($result instanceof Entity) {

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
        $dtoClass = $this->dtoClass;
        $entityClass = $this->entityClass;

        if ($dto instanceof $dtoClass && $entity instanceof $entityClass) {

            $result = $this->toDto($entity, $dto);

            if (is_array($result)) {

                $dto->map($result);

            } else if ($result instanceof Dto) {

                $dto = $result;
            }
        }

        return $dto;
    }
}
