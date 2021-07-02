<?php

namespace Miniyus\Mapper\Generate;

use JsonMapper;
use JsonMapper_Exception;

abstract class Template
{
    /**
     *
     *
     * @param Object $data [$data description]
     *
     * @return  $this        [return description]
     * @throws JsonMapper_Exception
     */
    public function map(Object $data): Template
    {
        return (new JsonMapper)->map($data, $this);
    }
}
