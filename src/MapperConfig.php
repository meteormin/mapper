<?php

namespace Miniyus\Mapper;

use Illuminate\Support\Arr;

/**
 * config/DataMapper.php의 데이터를 보다 쉽게 컨트롤하기 위한 클래스
 */
class MapperConfig
{
    /**
     * config/mapper.php 파일에 지정되어 있는 Dto class 리스트
     * @var string[]
     */
    protected array $dtos;

    /**
     * config/mapper.php 파일에 지정되어 있는 Entity class 리스트
     *
     * @var string[]
     */
    protected array $entities;

    /**
     * config/mapper.php 파일에 지정되어 있는 Map class 리스트
     *
     * @var string[]
     */
    protected array $maps;

    /**
     * config/mapper.php 배열
     *
     * @var array
     */
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;

        foreach ($this->config as $key => $map) {
            $this->maps[] = $key;
            $this->entities[] = $map['entity'] ?? null;
            $this->dtos[] = $map['dto'] ?? null;
        }
    }

    /**
     * get all config infomation
     *
     * @return string[]
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * find by key
     *
     * @param int $key
     *
     * @return array
     */
    public function find(int $key): array
    {
        return [
            'map' => $this->getMap($key),
            'dto' => $this->getDto($key),
            'entity' => $this->getEntity($key)
        ];
    }

    /**
     * find key by class name
     *
     * @param string $className
     *
     * @return string|null
     */
    public function findKeyByClass(string $className): ?string
    {
        $found = $this->match($className);

        return collect($found)->keys()->first();
    }

    /**
     * Undocumented function
     *
     * @param array|string $attributes
     * @param string|null $value
     *
     * @return array|string|null
     */
    public function findByAttribute($attributes, string $value = null)
    {
        $found = null;
        if (is_array($attributes)) {
            foreach ($attributes as $key => $attr) {

                $value = $this->where($key, $attr);

                if (is_array($value)) {
                    $found[$attr] = $value;
                } else {
                    $found[$key] = $value;
                }
            }
            return $found;
        } else if (is_string($attributes)) {
            return $this->where($attributes, $value);
        }

        return null;
    }

    /**
     * match key by class name
     *
     * @param string $className
     *
     * @return array
     */
    protected function match(string $className): array
    {
        return Arr::where($this->config, function ($value, $key) use ($className) {
            if ($key == $className) {
                return true;
            }

            $data = Arr::where($value, function ($v) use ($className) {
                return $v == $className;
            });

            if (count($data) == 0) {
                return false;
            }

            return true;
        });
    }

    /**
     * find
     *
     * @param string $key
     * @param string|null $attr
     *
     * @return string|string[]
     */
    protected function where(string $key, string $attr = null)
    {
        if (is_numeric($key)) {
            return $this->config[$attr];
        }

        if (is_null($attr)) {
            return $this->config[$key];
        }

        $getter = 'get' . ucfirst($attr);

        return $this->$getter($key);
    }

    // must only has getter

    /**
     * Get the value of dtos
     * @param int|null $key
     * @return string|string[]
     */
    public function getDto(int $key = null)
    {
        if (is_null($key)) {
            return (new static)->dtos;
        }
        return (new static)->dtos[$key];
    }

    /**
     * Get the value of entities
     * @param int|null $key
     * @return string|string[]
     */
    public function getEntity(int $key = null)
    {
        if (is_null($key)) {
            return (new static)->entities;
        }
        return (new static)->entities[$key];
    }

    /**
     * Get the value of maps
     * @param int|null $key
     * @return string|string[]
     */
    public function getMap(int $key = null)
    {
        if (is_null($key)) {
            return (new static)->maps;
        }
        return (new static)->maps[$key];
    }
}
