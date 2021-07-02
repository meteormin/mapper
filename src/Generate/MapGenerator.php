<?php

namespace Miniyus\Mapper\Generate;

use Miniyus\Mapper\Generate\Properties;
use JsonMapper_Exception;
use ReflectionException;

class MapGenerator extends Generator
{
    /**
     * class명
     * @var string
     */
    protected string $name;

    /**
     * json 원본
     *
     * @var string
     */
    protected string $json;

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
    protected MakeClass $maker;

    /**
     * 입력받은 문자열이 json인지 파악하고
     * json이면 json파일을 이용하여 map클래스 생성하고
     * json이 아니면 입력받은 클래스이름으로 json을 생성
     * 단, config.mapper.tables 리스트에 'snake case'로 정의된 동일이름의 테이블이 존재해야 한다.
     * @param string $name 클래스이름
     * @param string|null $json json파일
     * @throws JsonMapper_Exception
     */
    public function __construct(string $name, string $json = null)
    {
        if ($this->isJson($json)) {
            parent::__construct($name, $json);
        } else {
            $fileName = config('make_class.json_path') . '/maps/' . $name . '.json';
            $map = \Str::studly($name);
            $contents = config("mapper.maps.App\\Libraries\\MapperV2\\Maps\\{$map}");
            $contents['map'] = '';

            if (file_put_contents($fileName, json_encode($contents)) !== false) {
                parent::__construct($name, json_encode($contents));
            }
        }

        $this->template = new MapTemplate();
        $this->template->map(json_decode($this->json));
        $this->maker->setPath('app/Libraries/MapperV2/Maps');
    }

    /**
     * isJson
     * 해당 문자열이 json문자열인지 아닌지 체크
     *
     * @param string|null $str
     *
     * @return boolean
     */
    protected function isJson(?string $str): bool
    {
        return is_string($str) &&
            (is_object(json_decode($str)) ||
                is_array(json_decode($str)));
    }

    /**
     * 실행
     *
     * @return bool
     */
    public function generate(): bool
    {
        return $this->maker->make('Map', \Str::studly($this->name), $this->map());
    }

    /**
     * mapping을 위해 속성들이 존재하는지 검사하고
     * Maker(MakeClass)에게 넘길 매개변수 배열 생성
     *
     * @return array
     */
    protected function map(): array
    {
        if (is_null($this->template->getMap())) {
            $properties = $this->autoMapping();
        } else {
            $properties = $this->mapping();
        }

        return [
            'name' => $this->name,
            'entity' => $this->template->getEntity(),
            'dto' => $this->template->getDto(),
            'toEntity' => $this->toEntity($properties),
            'toDto' => $this->toDto($properties)
        ];
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

            $code .= "\$entity->{$setter}(\$dto->{$getter}());\n\t\t";
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

            $code .= "\$dto->{$setter}(\$entity->{$getter}());\n\t\t";
        }

        return $code;
    }

    /**
     * mapping
     *
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
