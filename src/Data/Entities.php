<?php

namespace Miniyus\Mapper\Data;


use Miniyus\Mapper\Data\Traits\ToDtos;
use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Miniyus\Mapper\Mapper;

/**
 * Entities
 * 라라벨 컬렉션을 확장(상속)
 * Entity 객체리스트를 라라벨 컬렉션의 내장 기능을 활용하여 보다 쉽게 컨트롤
 */
class Entities extends CustomCollection
{
    use ToDtos;

    /**
     * Undocumented variable
     *
     * @var Entity[]
     */
    protected $items = [];

    /**
     * @param array|Arrayable|ArrayAccess $entities
     */
    public function __construct($entities = [])
    {
        if (is_array($entities) || ($entities instanceof ArrayAccess) || ($entities instanceof Arrayable)) {
            parent::__construct($entities);
        }
    }

    /**
     * @param array|Arrayable|ArrayAccess $params
     * @return static
     */
    public static function newInstance($params = []): Entities
    {
        return new static($params);
    }

}
