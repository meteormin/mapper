<?php

namespace Miniyus\Mapper\Data;


use Miniyus\Mapper\Data\Traits\ToEntities;
use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

/**
 * Dtos
 * 라라벨 컬렉션을 확장(상속)
 * Dto객체리스트를 라라벨 컬렉션의 내장 기능을 활용하여 보다 쉽게 컨트롤
 */
class Dtos extends CustomCollection
{
    use ToEntities;

    /**
     * @var Dto[]
     */
    protected $items = [];

    /**
     * @var array|Collection
     */
    protected array $hidden = [];

    /**
     * @param array|Arrayable|ArrayAccess|null $dtos
     */
    public function __construct($dtos = [])
    {
        if (is_array($dtos) || ($dtos instanceof ArrayAccess) || ($dtos instanceof ArrayAccess)) {
            parent::__construct($dtos);
        }
    }

    /**
     * @param array|Arrayable|ArrayAccess $params
     * @return static
     */
    public static function newInstance($params = []): Dtos
    {
        return new static($params);
    }

}
