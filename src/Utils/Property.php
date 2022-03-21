<?php

namespace Miniyus\Mapper\Utils;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionObject;
use ReflectionProperty;
use Illuminate\Support\Str;

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
     * @param string $key
     *
     * @return mixed|null
     */
    public function getter(string $key)
    {
        return $this->origin->{'get' . Str::studly($key)}();
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setter(string $key, $value): Property
    {
        $this->origin->{'set' . Str::studly($key)}($value);

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
     * @return array|null
     */
    public function toArray(): ?array
    {
        $arr = [];

        foreach ($this->properties as $prop) {
            if (method_exists($this->origin, 'get' . Str::studly($prop->getName()))) {
                if ($prop->isInitialized($this->origin)) {
                    $arr[$prop->getName()] = $this->getter($prop->getName());
                }
            } else {
                if ($prop->isInitialized($this->origin)) {
                    $arr[$prop->getName()] = $this->getProperty($prop->getName());
                }
            }
        }

        return $arr;
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        $keys = [];
        foreach ($this->properties as $prop) {
            $keys[] = $prop->getName();
        }

        return $keys;
    }

    /**
     * @return array
     */
    public function values(): array
    {
        $values = [];

        foreach ($this->properties as $prop) {
            $values[] = $prop->getValue();
        }

        return $values;
    }

    /**
     * 기본 값 채우기
     * nullable 유형의 경우는 null
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
                        try {
                            $this->setter($property->getName(), null);
                        } catch (\BadMethodCallException $e) {
                            $this->setProperty($property->getName(), null);
                        }
                    } else if (!is_null($value)) {
                        try {
                            $this->setter($property->getName(), $value);
                        } catch (\BadMethodCallException $e) {
                            $this->setProperty($property->getName(), $value);
                        }
                    }

                } else {
                    $default = $defaults[$property->getName()];

                    if (gettype($default) == gettype($value)) {
                        try {
                            $this->setter($property->getName(), $default);
                        } catch (\BadMethodCallException $e) {
                            $this->setProperty($property->getName(), $default);
                        }
                    } else {

                        if ($type->allowsNull()) {
                            $setValue = null;
                        } else if (!is_null($value)) {
                            $setValue = $value;
                        }

                        if (isset($setValue)) {
                            try {
                                $this->setter($property->getName(), $setValue);
                            } catch (\BadMethodCallException $e) {
                                $this->setProperty($property->getName(), $setValue);
                            }
                        }
                    }
                }
            }
        }

    }

    /**
     * 타입 별 기본 값 설정
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
