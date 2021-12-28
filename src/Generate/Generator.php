<?php

namespace Miniyus\Mapper\Generate;


abstract class Generator
{
    /**
     * @var string
     */
    protected string $namespace;

    /**
     * @var string
     */
    protected string $name;

    /**
     * template
     *
     * @var Template
     */
    protected Template $template;

    /**
     * @var Maker
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
        $this->namespace = $namespace;
        $this->name = $name;
        $this->maker = $maker;
        $this->template = $template;
    }

    /**
     * 실행
     * 상황에 맞게 구현
     */
    abstract public function generate();

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Maker
     */
    public function getMaker(): Maker
    {
        return $this->maker;
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
