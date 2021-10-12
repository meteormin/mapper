<?php

namespace Miniyus\Mapper\Data;

use Miniyus\Mapper\Data\Contracts\Mapable;
use Illuminate\Support\Str;
use TypeError;

// 동적속성 객체
abstract class Dynamic implements Mapable
{
    /**
     * set 가능한 필드명 정의
     *
     * @var array
     */
    protected array $fillable = [];

    /**
     * 실제 데이터가 들어가는 배열
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * 새 객체 생성시, 픽스해둔 $fillable의 필드들이
     * snake_case가 아닌 경우, 에러가 발생할 수 있어서
     * 생성자에서 fill 메서드를 실행시켜 강제로 snake_case로 변환한다.
     */
    public function __construct(array $fill = null)
    {
        if (!is_null($fill)) {
            $this->fill($fill);
        }
    }

    /**
     * 동적 getter, setter
     *
     * @param string $name getter or setter method name
     * @param array $args arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $args)
    {
        if (substr($name, 0, 3) == 'get') {
            return $this->getAttribute(Str::snake(substr($name, 3)));
        }

        if (substr($name, 0, 3) == 'set') {
            $this->setAttribute(Str::snake(substr($name, 3)), $args[0]);
            return $this;
        }

        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return in_array($key, $this->fillable);
    }

    /**
     * 동적 속성 접근
     *
     * @param string $key 속성 명
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    /**
     * 동적 속성 값 설정
     *
     * @param string $key 속성 명
     * @param mixed $value 값
     *
     * @return $this
     */
    public function __set(string $key, $value)
    {
        $this->setAttribute($key, $value);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute(string $key)
    {
        if (isset($this->attributes[Str::snake($key)])) {
            return $this->attributes[Str::snake($key)];
        }
        return null;
    }

    /**
     * Undocumented function
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setAttribute(string $key, $value): Dynamic
    {
        if (in_array(Str::snake($key), $this->fillable)) {
            $this->attributes[Str::snake($key)] = $value;
        } else {
            throw new TypeError('존재하지 않는 속성, ' . Str::snake($key));
        }
        return $this;
    }

    public function setFillable(array $fillable)
    {
        $fillable = collect($fillable)->map(function ($item) {
            return Str::snake($item);
        });

        $this->fillable = $fillable->all();
    }

    public function getFillable(): array
    {
        return $this->fillable;
    }

    /**
     * set attributes
     *
     * @param array $input
     *
     * @return $this
     */
    public function fill(array $input): Dynamic
    {
        foreach ($input as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * @param bool $allowNull
     * @return array
     */
    public function toArray(bool $allowNull = null): array
    {
        return $this->attributes;
    }

    /**
     * @param int $options
     * @return false|string
     */
    public function toJson($options = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES): string
    {
        return json_encode($this->attributes);
    }

    /**
     * @param $json
     * @return $this
     */
    public function fromJson($json): Dynamic
    {
        return $this->fill(json_decode($json, true));
    }
}
