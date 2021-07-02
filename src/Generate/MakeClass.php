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

    public function __construct(string $path = null)
    {
        $this->stubPath = base_path(config('make_class.stub_path', 'app/Stubs'));
        $this->path = base_path(config('make_class.save_path', 'app'));

        parent::__construct($path);
    }
}
