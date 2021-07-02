<?php


namespace Miniyus\Mapper\Data\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;

interface Mapable extends Arrayable, Jsonable
{
    /**
     * @param $data
     * @param callable|Closure|null $callback
     * @return $this
     */
    public function map($data, $callback = null): Mapable;

    /**
     * @param $data
     * @param callable|Closure|null $callback
     * @return Collection|array
     */
    public function mapList($data, $callback = null);

    /**
     * @param bool $allowNull
     * @return array
     */
    public function toArray(bool $allowNull = false): ?array;

    /**
     * @param int $options
     * @param false $allowNull
     * @return string
     */
    public function toJson($options = 0, bool $allowNull = false): string;
}
