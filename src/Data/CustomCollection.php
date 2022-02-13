<?php


namespace Miniyus\Mapper\Data;


use Miniyus\Mapper\Data\Contracts\Mapable;
use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Miniyus\Mapper\Data\Traits\Transformation;

/**
 * @template TKey of array-key
 * @template TValue
 */
class CustomCollection extends Collection
{
    use Transformation {
        makeHidden as traitMakeHidden;
        makeVisible as traitMakeVisible;
    }

    /**
     * @var array<TKey, TValue>
     */
    protected $items = [];

    /**
     * @var string[]
     */
    protected array $hidden = [];

    /**
     * CustomCollection constructor.
     * @inheritdoc
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
    }

    /**
     * makeHidden
     * toArray, toJson, __toString 메서드를 통한 출력결과에서 숨길 속성을 정의할 수 있다
     * @param string[]|string $hidden
     *
     * @return $this
     */
    public function makeHidden(...$hidden): self
    {
        $this->traitMakeHidden(is_array($hidden[0]) ? $hidden[0] : $hidden);

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
        })->values()->all();

        return $this;
    }

    /**
     * makeVisible
     * makeHidden 메서드에서 숨긴 속성을 다시 출력 가능하게
     * @param string[]|string $visible
     *
     * @return $this
     */
    public function makeVisible(...$visible): self
    {
        $this->traitMakeVisible(is_array($visible[0]) ? $visible[0] : $visible);

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
     * @return array<TKey, TValue>
     */
    public function toArray(bool $allowNull = null): array
    {
        return $this->map(function ($item) use ($allowNull) {
            if ($item instanceof Mapable) {
                return $item->toArray($allowNull);
            } else if ($item instanceof Arrayable) {
                return $item->toArray();
            }
            return $item;
        })->values()->all();
    }
}
