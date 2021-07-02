<?php


namespace Miniyus\Mapper\Data;


use Miniyus\Mapper\Data\Traits\Transformation;
use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class CustomCollection extends Collection
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var array
     */
    protected array $hidden = [];

    /**
     * CustomCollection constructor.
     * @param array|Arrayable|ArrayAccess $items
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
    }

    /**
     * makeHidden
     * toArray, toJson, __toString 메서드를 통한 출력결과에서 숨길 속성을 정의할 수 있다
     * @param array|string $hidden
     *
     * @return $this
     */
    public function makeHidden($hidden): self
    {
        $collection = collect($this->hidden);

        $this->hidden = $collection->merge($hidden)->all();

        $this->items = $this->map(function ($item) {
            if (method_exists($item, 'makeHidden')) {
                return $item->makeHidden($this->hidden);
            } else if ($item instanceof Arrayable) {
                $collection = collect($item->toArray())->except($this->hidden)->all();
                $class = get_class($item);
                return DataMapper::map($collection, new $class);
            } else if (is_array($item)) {
                return collect($item)->except($this->hidden)->all();
            } else {
                return $item;
            }
        })->all();

        return $this;
    }

    /**
     * makeVisible
     * makeHidden 메서드에서 숨긴 속성을 다시 출력 가능하게
     * @param array|string $visible
     *
     * @return $this
     */
    public function makeVisible($visible): self
    {
        $collection = collect($this->hidden);

        $this->hidden = $collection->filter(function ($item) use ($visible) {
            if (is_array($visible)) {
                $cond = false;
                foreach ($visible as $val) {
                    if ($item != $val) {
                        $cond = true;
                    } else {
                        return false;
                    }
                }
                return $cond;
            }

            return $item != $visible;
        })->all();

        $this->items = $this->map(function ($item) {
            if (method_exists($item, 'makeVisible')) {
                return $item->makeVisible($this->hidden);
            } else {
                return $item;
            }
        });

        return $this;
    }

    /**
     * @param bool $allowNull
     * @return array
     */
    public function toArray(bool $allowNull = false): array
    {
        return $this->map(function ($item) use ($allowNull) {
            if ($item instanceof Transformation) {
                return $item->toArray($allowNull);
            } else if ($item instanceof Arrayable) {
                return $item->toArray();
            }
            return $item;
        })->values()->all();
    }

    /**
     * @param int $options
     * @param bool $allowNull
     * @return string
     */
    public function toJson($options = JSON_UNESCAPED_UNICODE, bool $allowNull = false): string
    {
        return json_encode($this->toArray());
    }
}
