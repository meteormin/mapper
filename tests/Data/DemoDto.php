<?php

namespace Data;

use Miniyus\Mapper\Data\Dto;

class DemoDto extends Dto
{
    private int $id;
    private string $name;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return DemoDto
     */
    public function setId(int $id): DemoDto
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return DemoDto
     */
    public function setName(string $name): DemoDto
    {
        $this->name = $name;
        return $this;
    }
}