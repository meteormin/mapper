<?php

namespace Miniyus\Mapper\Generate;

use ReflectionException;

class MapGenerator extends Generator
{
    /**
     * @var string
     */
    protected string $namespace;

    /**
     * class명
     * @var string
     */
    protected string $name;

    /**
     * json 원본
     *
     * @var array|object
     */
    protected $json;

    /**
     * \App\Libraries\Generate\MapTemplate
     *
     * @var MapTemplate
     */
    protected Template $template;

    /**
     * make class
     * @var MakeClass
     */
    protected Maker $maker;

    /**
     * @param string $namespace
     * @param string $name
     * @param Maker $maker
     * @param Template $template
     */
    public function __construct(string $namespace, string $name, Maker $maker, Template $template)
    {
        parent::__construct($namespace, $name, $maker, $template);
    }

    /**
     * 실행
     *
     * @return bool
     * @throws ReflectionException
     */
    public function generate(): bool
    {
        return $this->maker->make('Map', \Str::studly($this->name), $this->makeParameters()->toArray());
    }

    /**
     * mapping을 위해 속성들이 존재하는지 검사하고
     * Maker(MakeClass)에게 넘길 매개변수 배열 생성
     *
     * @return MapStub
     * @throws ReflectionException
     */
    protected function makeParameters(): MapStub
    {
        if (is_null($this->template->getMap())) {
            $properties = $this->autoMapping();
        } else {
            $properties = $this->mapping();
        }

        $namespace = $this->namespace;
        $name = $this->name;
        $fullNameEntity = $this->template->getEntity();
        $fullNameDto = $this->template->getDto();
        $entity = \Str::of($fullNameEntity)->afterLast('\\');
        $dto = \Str::of($fullNameDto)->afterLast('\\');
        $toDto = $this->toDto($properties);
        $toEntity = $this->toEntity($properties);

        return new MapStub(
            $namespace,
            $name,
            $fullNameDto,
            $fullNameEntity,
            $dto,
            $entity,
            $toEntity,
            $toDto
        );
    }


    /**
     * dto의 reflection 클래스
     *
     * @return Properties
     * @throws ReflectionException
     */
    protected function dto(): Properties
    {
        return new Properties($this->template->getDto());
    }

    /**
     * entity의 reflection 클래스
     *
     * @return Properties
     * @throws ReflectionException
     */
    protected function entity(): Properties
    {
        return new Properties($this->template->getEntity());
    }

    /**
     * make sttter stirng
     *
     * @param string $key
     *
     * @return string
     */
    protected function makeSetter(string $key): string
    {
        return 'set' . ucfirst($key);
    }

    /**
     * make getter string
     *
     * @param string $key
     *
     * @return string
     */
    protected function makeGetter(string $key): string
    {
        return 'get' . ucfirst($key);
    }

    /**
     * toEntity에 들어갈 문자열을 생성
     *
     * @param array $map
     *
     * @return string
     */
    protected function toEntity(array $map): string
    {
        $code = '';
        foreach ($map as $key => $value) {
            $setter = $this->makeSetter($key);
            $getter = $this->makeGetter(!empty($value) ? $value : $key);

            $code .= "\$entity->{$setter}(\$dto->{$getter}());\n\t\t\t";
        }

        return $code;
    }

    /**
     * toDto에 들어갈 문자열을 생성
     *
     * @param array $map
     *
     * @return string
     */
    protected function toDto(array $map): string
    {
        $code = '';
        foreach ($map as $key => $value) {
            $getter = $this->makeGetter($key);
            $setter = $this->makeSetter(!empty($value) ? $value : $key);

            $code .= "\$dto->{$setter}(\$entity->{$getter}());\n\t\t\t";
        }

        return $code;
    }

    /**
     * mapping
     * property(은)는 Entity Class(이)가 기준이 된다.
     * @return array
     * @throws ReflectionException
     */
    protected function mapping(): array
    {
        $entity = $this->entity();
        $dto = $this->dto();

        $properties = [];

        foreach ($this->template->getMap() as $key => $value) {
            $entityProp = null;
            $dtoProp = null;

            if ($entity->hasProperty($key)) {
                $entityProp = $key;

                if ($dto->hasProperty($value)) {
                    $dtoProp = $value;
                }

                $properties[$entityProp] = $dtoProp;
            }
        }

        return $properties;
    }

    /**
     * 맵객체가 비어있는 경우
     *
     * @return array
     * @throws ReflectionException
     */
    protected function autoMapping(): array
    {
        $entity = $this->entity();
        $dto = $this->dto();

        $properties = [];

        foreach ($entity->getNames() as $value) {
            if ($dto->hasProperty($value)) {
                $properties[$value] = $value;
            }
        }

        return $properties;
    }
}
