<?php

namespace Miniyus\Mapper\Data;


use Miniyus\Mapper\Data\Traits\ToEntities;
use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Dtos
 * 라라벨 컬렉션을 확장(상속)
 * Dto객체리스트를 라라벨 컬렉션의 내장 기능을 활용하여 보다 쉽게 컨트롤
 * @template TKey of array-key
 * @template TValue
 */
class Dtos extends CustomCollection
{
    use ToEntities;

    /**
     * @inheritdoc
     */
    protected $items = [];

    /**
     * @var string[]
     */
    protected array $hidden = [];

    /**
     * @param array<TKey,TValue>|Arrayable<TKey,TValue>|ArrayAccess<TKey,TValue>|null $dtos
     */
    public function __construct($dtos = [])
    {
        if (is_array($dtos) || ($dtos instanceof Arrayable) || ($dtos instanceof ArrayAccess)) {
            parent::__construct($dtos);
        }
    }

    /**
     * @param array<TKey,TValue>|Arrayable<TKey,TValue>|ArrayAccess<TKey,TValue>|null  $params
     * @return static
     */
    public static function newInstance($params = []): Dtos
    {
        return new static($params);
    }

}
