<?php

namespace Miniyus\Mapper\Generate;

abstract class Generator
{

    protected string $name;

    /**
     * json
     *
     * @var string|null
     */
    protected string $json;

    /**
     * template
     *
     * @var Template
     */
    protected Template $template;

    /**
     *
     *
     * @var MakeClass
     */
    protected MakeClass $maker;

    public function __construct(string $name, string $json)
    {
        $this->name = $name;
        $this->json = $json;
        $this->maker = new MakeClass();
    }

    /**
     * 실행
     * 상황에 맞게 구현
     */
    abstract public function generate();

    /**
     * get class name
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * get origin json
     *
     * @return string|null
     */
    public function getJson(): ?string
    {
        return $this->json;
    }

    /**
     * get template
     *
     * @return Template|null
     */
    public function getTemplate(): ?Template
    {
        return $this->template;
    }
}
