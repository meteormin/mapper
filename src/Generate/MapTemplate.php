<?php

namespace Miniyus\Mapper\Generate;

class MapTemplate extends Template
{
    /**
     * dto
     *
     * @var string|null
     */
    protected ?string $dto;

    /**
     * entity
     *
     * @var string|null
     */
    protected ?string $entity;

    /**
     * map
     *
     * @var array|null
     */
    protected ?array $map;

    /**
     * Get dto
     *
     * @return  string|null
     */
    public function getDto(): ?string
    {
        return $this->dto;
    }

    /**
     * Set dto
     *
     * @param  string|null  $dto  dto
     *
     * @return  self
     */
    public function setDto(?string $dto): MapTemplate
    {
        $this->dto = $dto;

        return $this;
    }

    /**
     * Get entity
     *
     * @return  string|null
     */
    public function getEntity(): ?string
    {
        return $this->entity;
    }

    /**
     * Set entity
     *
     * @param  string|null  $entity  entity
     *
     * @return  self
     */
    public function setEntity(?string $entity): MapTemplate
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get map
     *
     * @return  array|null
     */
    public function getMap(): ?array
    {
        return $this->map;
    }

    /**
     * Set map
     *
     * @param  array|null  $map  map
     *
     * @return  self
     */
    public function setMap(?array $map): MapTemplate
    {
        $this->map = $map;

        return $this;
    }
}
