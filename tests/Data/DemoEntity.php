<?php

namespace Data;

class DemoEntity extends \Miniyus\Mapper\Data\Entity
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
     * @return DemoEntity
     */
    public function setId(int $id): DemoEntity
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
     * @return DemoEntity
     */
    public function setName(string $name): DemoEntity
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getIdentifier(): string
    {
        return 'demo';
    }
}