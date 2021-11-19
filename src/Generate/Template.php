<?php

namespace Miniyus\Mapper\Generate;

use JsonMapper;
use JsonMapper_Exception;

abstract class Template
{
    /**
     * @param object|array $data
     * @throws JsonMapper_Exception
     */
    public function __construct($data)
    {
        $this->map($data);
    }

    /**
     * @param $data
     * @return Template
     * @throws JsonMapper_Exception
     */
    public function map($data): Template
    {
        return (new JsonMapper)->map($data, $this);
    }
}
