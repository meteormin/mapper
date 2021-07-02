<?php

namespace Miniyus\Mapper\Generate;

use ReflectionClass;
use ReflectionException;

/**
 * ReflectionProerty를 보다 쉽게 다루기 위함
 */
class Properties
{
    /**
     * reflection
     *
     * @var ReflectionClass
     */
    protected ReflectionClass $object;

    /**
     * reflection properties
     *
     * @var array
     */
    protected array $properties;

    /**
     *
     *
     * @param string|null $class
     * @throws ReflectionException
     */
    public function __construct(?string $class)
    {
        if (empty($class)) {
            $class = '\StdClass';
        }

        $this->object = new ReflectionClass($class);
        $this->properties = $this->object->getProperties();
    }

    /**
     * get Value
     *
     * @param string $key
     *
     * @return mixed|null
     * @throws ReflectionException
     */
    protected function getValue(string $key)
    {
        $prop = $this->object->getProperty($key);

        return $prop->getValue();
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
     * get property names
     *
     * @return array
     */
    public function getNames(): array
    {
        $arr = [];
        foreach ($this->properties as $prop) {
            $arr[] = $prop->getName();
        }

        return $arr;
    }

    /**
     * to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $arr = [];
        foreach ($this->properties as $prop) {
            $arr[$prop->getName()] = $prop->getValue();
        }

        return $arr;
    }
}
