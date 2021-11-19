<?php

namespace Miniyus\Mapper\Generate;

class MakeClass extends Maker
{
    /**
     * 클래스가 생성될 경로
     * @var string
     */
    protected string $path;

    /**
     * stub 파일 경로
     * @var string
     */
    protected string $stubPath;

    /**
     * @param string|null $stubPath
     * @param string|null $path
     * @param string $ext
     */
    public function __construct(string $stubPath = null, string $path = null, string $ext = 'php')
    {
        parent::__construct($stubPath, $path, $ext);
    }
}
