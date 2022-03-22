<?php

namespace Miniyus\Mapper;

use ArrayAccess;
use Illuminate\Support\Arr;
use Miniyus\Mapper\Data\Contracts\Configuration;

/**
 * config/DataMapper.php의 데이터를 보다 쉽게 컨트롤하기 위한 클래스
 */
class MapperConfig implements Configuration
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

        foreach ($this->config['maps'] as $key => $map) {
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
     * @param int|string|null $key
     * @param mixed|null $default
     * @return array|ArrayAccess|mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * find by key
     *
     * @param int $key
     *
     * @return array
     */
    public function findMap(int $key): array
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
    public function findMapByClass(string $className): ?string
    {
        $found = $this->matchByMaps($className);

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
    public function findByMap($attributes, string $value = null)
    {
        $found = null;
        if (is_array($attributes)) {
            foreach ($attributes as $key => $attr) {

                $value = $this->whereIsMap($key, $attr);

                if (is_array($value)) {
                    $found[$attr] = $value;
                } else {
                    $found[$key] = $value;
                }
            }
            return $found;
        } else if (is_string($attributes)) {
            return $this->whereIsMap($attributes, $value);
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
    protected function matchByMaps(string $className): array
    {
        return $this->filter(function ($value, $key) use ($className) {
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
    protected function whereIsMap(string $key, string $attr = null)
    {
        if (is_numeric($key)) {
            return $this->config['maps'][$attr];
        }

        if (is_null($attr)) {
            return $this->config['maps'][$key];
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
            return $this->dtos;
        }
        return $this->dtos[$key];
    }

    /**
     * Get the value of entities
     * @param int|null $key
     * @return string|string[]
     */
    public function getEntity(int $key = null)
    {
        if (is_null($key)) {
            return $this->entities;
        }
        return $this->entities[$key];
    }

    /**
     * Get the value of maps
     * @param int|null $key
     * @return string|string[]
     */
    public function getMap(int $key = null)
    {
        if (is_null($key)) {
            return $this->maps;
        }
        return $this->maps[$key];
    }

    /**
     * @param string|array $keys
     * @return bool
     */
    public function has($keys): bool
    {
        return Arr::has($this->config, $keys);
    }

    public function filter(callable $callback): array
    {
        return Arr::where($this->config, $callback);
    }
}
