<?php

namespace Miniyus\Mapper\Generate;

use JsonMapper;
use JsonMapper_Exception;

abstract class Template
{
    /**
     * @param object $data
     * @throws JsonMapper_Exception
     */
    public function __construct(object $data)
    {
        $this->map($data);
    }

    /**
     * @param object $data
     * @return Template
     * @throws JsonMapper_Exception
     */
    public function map(object $data): Template
    {
        return (new JsonMapper)->map($data, $this);
    }
}
