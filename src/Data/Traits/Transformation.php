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
     * to string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

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
     * @param boolean|null $allowNull
     * @return array|null
     * @version 2.5.5 allow null, case style 설정 추가
     */
    public function toArray(bool $allowNull = null): ?array
    {
        $property = new Property($this);

        if (config('mapper.transformation.case_style') == 'camel_case') {
            $attributes = ArrController::camelFromArray($property->toArray());
        } else {
            $attributes = ArrController::snakeFromArray($property->toArray());
        }

        if (is_null($allowNull)) {
            $allowNull = config('mapper.transformation.allow_null');
        }

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
     * @param int|string $options
     * @return string
     * @version 2.5.8 allowNull 파라미터 제거
     */
    public function toJson($options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT): string
    {
        return json_encode($this, $options);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray() ?? [];
    }

    /**
     * makeHidden
     * toArray() 메서드 작동 시, 숨기고 싶은 속성을 정할 수 있다
     * @param string|array|null $hidden
     * @return $this
     * @version 2.5.8 작동 방식 변경
     */
    public function makeHidden(...$hidden): self
    {
        $this->hidden = array_merge(
            $this->hidden, is_array($hidden[0]) ? $hidden[0] : $hidden
        );

        return $this;
    }

    /**
     * toArray() 메서드 작동 시, 숨겼던 속성 항목을 다시 출력 시킬 수 있다.
     * @param string|array|null $visible
     * @return $this
     * @version 2.5.8 작동 방식 변경
     */
    public function makeVisible(...$visible): self
    {
        $this->hidden = array_diff(
            $this->hidden, is_array($visible[0]) ? $visible[0] : $visible
        );
        return $this;
    }

    /**
     * 초기화 되지 않은 속성을 타입에 맞춰 초기화
     * int: 0
     * string: ''
     * bool: false
     * array: []
     * 기타: null (이 경우 nullable체크를 하여 nullable인 경우만 초기화를 해준다.)
     * @param array $defaults
     * @return $this
     */
    public function initialize(array $defaults = []): self
    {
        $property = new Property($this);
        $property->fillDefault($defaults);

        return $this;
    }
}
