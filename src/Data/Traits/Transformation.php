<?php

namespace Miniyus\Mapper\Data\Traits;

use Miniyus\Mapper\Utils\ArrController;
use Miniyus\Mapper\Utils\Property;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Trait Transformation
 * 기능 위주 구현
 * @package App\Libraries\Data\Traits
 */
trait Transformation
{
    /**
     * @var array
     */
    protected array $hidden = [];

    /**
     * @param array $hidden
     * @return $this
     */
    public function setHidden(array $hidden): self
    {
        return $this->makeHidden($hidden);
    }

    /**
     * Get 출력하지 않을 속성들의 배열
     *
     * @return  array
     */
    public function getHidden(): array
    {
        return $this->hidden;
    }

    /**
     * 배열로 변환
     * $allowNull 파라미터가 true면 null인 필드도 배열로 리턴
     * @param boolean $allowNull
     * @return array|null
     */
    public function toArray(bool $allowNull = false): ?array
    {
        $property = new Property($this);

        $attributes = ArrController::snakeFromArray($property->toArray());

        if (!$allowNull) {
            $attributes = ArrController::exceptNull($attributes);
        }

        $attributes = collect($attributes)->map(function ($item) {

            if ($item instanceof Arrayable) {
                return $item->toArray();
            }

            return $item;
        });

        $attributes = $attributes->except(array_merge(['hidden'], $this->hidden));

        return $attributes->isEmpty() ? null : $attributes->all();
    }

    /**
     * @param int $options
     * @param bool $allowNull
     * @return string
     */
    public function toJson($options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT, bool $allowNull = false): string
    {
        return json_encode($this, $options);
    }

    /**
     * to string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * [makeHidden description]
     * toArray() 메서드 작동 시, 숨기고 싶은 속성을 정할 수 있다
     * @param string|array $hidden
     * @return $this
     */
    public function makeHidden($hidden): self
    {
        $collection = collect($this->hidden);

        $this->hidden = $collection->merge($hidden)->all();

        return $this;
    }

    /**
     * toArray() 메서드 작동 시, 숨겼던 속성 항목을 다시 출력 시킬 수 있다.
     * 파라미터를 넣지 않으면 Hidden 속성 전부를 Visible 속성으로 만들 수 있다.
     * @param string|array|null $visible
     * @return $this
     */
    public function makeVisible($visible = null): self
    {
        if (is_null($visible)) {
            $this->hidden = [];
            return $this;
        }

        $collection = collect($this->hidden);

        $this->hidden = $collection->filter(function ($item) use ($visible) {
            $cond = false;
            if (is_array($visible)) {
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

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray() ?? [];
    }
}
