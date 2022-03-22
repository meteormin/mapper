<?php

namespace Data;

use Miniyus\Mapper\Data\Dto;
use Miniyus\Mapper\Data\Entity;

require_once './tests/Data/DemoDto.php';
require_once './tests/Data/DemoEntity.php';

class DemoMap extends \Miniyus\Mapper\Maps\Map
{

    /**
     * @inheritDoc
     * @param DemoDto $dto
     * @param DemoEntity $entity
     */
    protected function toEntity(Dto $dto, Entity $entity)
    {
        $entity->setId($dto->getId());
        $entity->setName($dto->getName());

        return $entity;
    }

    /**
     * @inheritDoc
     * @param DemoDto $dto
     * @param DemoEntity $entity
     */
    protected function toDto(Entity $entity, Dto $dto)
    {
        $dto->setId($entity->getId());
        $dto->setName($entity->getName());

        return $dto;
    }
}