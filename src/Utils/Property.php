<?php

namespace Miniyus\Mapper\Utils;

use ReflectionClass;
use ReflectionObject;
use ReflectionProperty;
use Throwable;

/**
 * public이 아닌 속성정보와 데이터에 접근하기 위한 클래스
 * 단, getter와 setter가 구현되어 있어야 함
 * ReflectionClass 기능을 사용하여 조금 더 쉽게 사용
 */
class Property
{
    /**
     * Object
     *
     * @var Object
     */
    protected object $origin;
    /**
     * reflection
     *
     * @var ReflectionClass
     */
    protected ReflectionClass $object;

    /**
     * reflection properties
     *
     * @var ReflectionProperty[]
     */
    protected array $properties;

    /**
     * Property constructor.
     * @param Object $class
     */
    public function __construct(object $class)
    {
        $this->origin = $class;
        $this->object = new ReflectionObject($class);
        $this->setProperties($this->object->getProperties());
    }


    /**
     * Undocumented function
     * @param ReflectionProperty[] $properties
     * @return $this
     */
    protected function setProperties(array $properties): Property
    {
        foreach ($properties as $prop) {
            $prop->setAccessible(true);
            $this->properties[] = $prop;
        }

        return $this;
    }

    /**
     * get value of key
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function get(string $key)
    {
        return $this->origin->{'get' . ucfirst($key)}();
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function set(string $key, $value): Property
    {
        $this->origin->{'set' . ucfirst($key)}($value);

        return $this;
    }

    /**
     * check has property
     *
     * @param string $key
     *
     * @return boolean
     */
    public function hasProperty(string $key): bool
    {
        return $this->object->hasProperty($key);
    }

    /**
     * to array
     *
     * @return array|null
     */
    public function toArray(): ?array
    {
        $arr = [];
        foreach ($this->properties as $prop) {
            if (method_exists($this->origin, 'get' . \Str::studly($prop->getName()))) {
                if ($prop->isInitialized($this->origin)) {
                    $arr[$prop->getName()] = $this->origin->{'get' . \Str::studly($prop->getName())}();
                }
            }
        }

        return $arr;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function toArrayKeys(): array
    {
        $keys = [];
        foreach ($this->properties as $prop) {
            $keys[] = $prop->getName();
        }

        return $keys;
    }
}
