<?php

namespace Data;

class DemoEntity extends \Miniyus\Mapper\Data\Entity
{

    /**
     * @inheritDoc
     */
    protected function getIdentifier(): string
    {
        return 'demo';
    }
}