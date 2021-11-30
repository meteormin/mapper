<?php

namespace Miniyus\Mapper\Utils;


use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionObject;
use ReflectionProperty;

/**
 * public이 아닌 속성정보와 데이터에 접근하기 위한 클래스
 * 단, getter와 setter가 구현되어 있어야 함
 * ReflectionClass 기능을 사용하여 조금 더 쉽게 사용
 */

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
    protected ReflectionClass $reflection;

    /**
     * reflection properties
     *
     * @var ReflectionProperty[]
     */
    protected array $properties;

    /**
     * Property constructor.
     * @param Object $object
     */
    public function __construct(object $object)
    {
        $this->origin = $object;
        $this->reflection = new ReflectionObject($object);
        $this->setProperties($this->reflection->getProperties());
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
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
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
        $getter = 'get' . ucfirst($key);
        if (method_exists($this->origin, $getter)) {
            return $this->origin->$getter();
        }

        return $this->getProperty($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function set(string $key, $value): Property
    {
        $setter = 'set' . ucfirst($key);
        if (method_exists($this->origin, $setter)) {
            $this->origin->$setter($value);
            return $this;
        }

        return $this->setProperty($key, $value);
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
        return $this->reflection->hasProperty($key);
    }

    /**
     * @param string $key
     * @return ReflectionProperty|null
     */
    public function getProperty(string $key): ?ReflectionProperty
    {
        if ($this->hasProperty($key)) {
            $properties = $this->getProperties();
            foreach ($properties as $property) {
                if ($property instanceof ReflectionProperty) {
                    if ($property->getName() == $key) {
                        return $property;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    public function setProperty(string $key, $value): Property
    {
        $property = $this->getProperty($key);
        if (!is_null($property)) {
            $property->setValue($this->origin, $value);
        }

        return $this;
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
            if (method_exists($this->origin, 'get' . Str::studly($prop->getName()))) {
                if ($prop->isInitialized($this->origin)) {
                    $arr[$prop->getName()] = $this->origin->{'get' . Str::studly($prop->getName())}();
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

    /**
     * @param array $defaults
     * @return void
     */
    public function fillDefault(array $defaults = [])
    {
        foreach ($this->getProperties() as $property) {
            if (!$property->hasType()) {
                continue;
            }

            if ($property->isInitialized($this->origin)) {
                continue;
            }

            $type = $property->getType();
            if ($type instanceof ReflectionNamedType) {
                $value = $this->getDefaultValue($type->getName());
                if (empty($defaults)) {
                    if ($type->allowsNull()) {
                        $this->set($property->getName(), null);
                    } else if (!is_null($value)) {
                        $this->set($property->getName(), $value);
                    }

                } else {
                    $default = $defaults[$property->getName()];

                    if (gettype($default) == gettype($value)) {
                        $this->set($property->getName(), $default);
                    } else {
                        if (!is_null($value)) {
                            $this->set($property->getName(), $value);
                        } else if ($type->allowsNull()) {
                            $this->set($property->getName(), $value);
                        }
                    }
                }
            }
        }

    }

    /**
     * @param string $type
     * @return array|false|float|int|string|null
     */
    public function getDefaultValue(string $type)
    {
        switch ($type) {
            case 'int':
                return 0;
            case 'float':
                return 0.0;
            case 'string':
                return '';
            case 'bool':
                return false;
            case 'array':
                return [];
            default:
                return null;
        }
    }
}
