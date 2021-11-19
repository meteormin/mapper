<?php

namespace Miniyus\Mapper\Generate;

use Illuminate\Contracts\Support\Arrayable;

class MapStub implements Arrayable
{
    public string $namespace;
    public string $name;
    public string $fullNameDto;
    public string $fullNameEntity;
    public string $dto;
    public string $entity;
    public string $toEntity;
    public string $toDto;

    /**
     * @param string $namespace
     * @param string $name
     * @param string $fullNameDto
     * @param string $fullNameEntity
     * @param string $dto
     * @param string $entity
     * @param string $toEntity
     * @param string $toDto
     */
    public function __construct(
        string $namespace,
        string $name,
        string $fullNameDto,
        string $fullNameEntity,
        string $dto,
        string $entity,
        string $toEntity,
        string $toDto
    )
    {
        $this->namespace = $namespace;
        $this->name = $name;
        $this->fullNameDto = $fullNameDto;
        $this->fullNameEntity = $fullNameEntity;
        $this->dto = $dto;
        $this->entity = $entity;
        $this->toEntity = $toEntity;
        $this->toDto = $toDto;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'namespace' => $this->namespace,
            'name' => $this->name,
            'fullNameDto' => $this->fullNameDto,
            'fullNameEntity' => $this->fullNameEntity,
            'dto' => $this->dto,
            'entity' => $this->entity,
            'toEntity' => $this->toEntity,
            'toDto' => $this->toDto
        ];
    }
}