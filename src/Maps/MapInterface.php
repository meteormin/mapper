<?php

namespace Miniyus\Mapper\Maps;

use Miniyus\Mapper\Data\Dto;
use Miniyus\Mapper\Data\Entity;

interface MapInterface
{
    /**
     * Undocumented function
     *
     * @param Entity $entity
     * @param Dto $dto
     * @return Dto|array
     */
    public function entityToDto(Entity $entity, Dto $dto);

    /**
     * Undocumented function
     *
     * @param Dto $dto
     * @param Entity $entity
     * @return Entity|array
     */
    public function dtoToEntity(Dto $dto, Entity $entity);
}
