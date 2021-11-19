<?php

namespace Miniyus\Mapper\Generate;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use TypeError;

class Maker
{
    /**
     * 새로 생성될 경로
     * @var string
     */
    protected string $path;
    /**
     * stub 파일 경로
     * @var string
     */
    protected string $stubPath;

    /**
     * 새로 생성할 파일의 확장자
     *
     * @var string
     */
    protected string $ext;

    public function __construct(string $stubPath, string $path, string $ext = 'php')
    {
        $this->stubPath = $stubPath;
        $this->setPath($path);
        $this->ext = $ext;
    }

    /**
     * 클래스 경로 설정
     * @param string $path
     *
     * @return $this
     */
    public function setPath(string $path): Maker
    {
        $this->path = base_path($path);

        if (!is_dir($this->stubPath)) {
            mkdir($this->stubPath, 0755, true);
        }

        return $this;
    }

    /**
     * make
     * 실행
     *
     * @param string $class 생성할 stub파일 이름
     * @param string $name 생성할 클래스(파일)이름
     * @param array $parameters stub파일의 대입될 매개변수들
     *
     * @return bool
     */
    public function make(string $class, string $name, array $parameters): bool
    {
        if ($this->putFile($name, $class, $parameters)) {
            return true;
        }

        return false;
    }

    /**
     * 파일 저장 하지 않고 stub파일 내용만 생성
     *
     * @param string $class
     * @param array $parameters
     *
     * @return string|null
     */
    public function run(string $class, array $parameters): ?string
    {
        $class = "{$this->stubPath}/{$class}.stub";

        $this->validation($class, $parameters);

        return $this->write($class, $parameters);
    }

    /**
     * 파일 생성
     *
     * @param string $name
     * @param string $class
     * @param array $parameters
     *
     * @return int|false
     */
    protected function putFile(string $name, string $class, array $parameters)
    {
        $name = "{$this->path}/{$name}.{$this->ext}";

        return file_put_contents($name, $this->run($class, $parameters));
    }

    /**
     * 파일에 들어갈 내용 생성
     *
     * @param string $filename
     * @param array $parameters
     *
     * @return string
     */
    protected function write(string $filename, array $parameters): string
    {
        $contents = file_get_contents($filename);

        return strtr($contents, $this->convertParameters($parameters));
    }

    /**
     * 파라미터형식을 stub에 맞게 변환
     *
     * @param array $parameters
     *
     * @return array
     */
    protected function convertParameters(array $parameters): array
    {
        $converted = [];
        foreach ($parameters as $key => $value) {
            $converted["{{{$key}}}"] = $value;
        }

        return $converted;
    }

    /**
     * 파리미터 유효성 검사
     * 유효하면 넘어가고, 아니면 TypeErrorException을 던진다.
     * @param string $class
     * @param array $parameters
     *
     * @return void
     * @throws TypeError
     */
    protected function validation(string $class, array $parameters)
    {
        if (file_exists($class)) {
            $validated = collect(Arr::where($parameters, function ($value, $key) use ($class) {
                return strpos(file_get_contents($class), "{{{$key}}}") !== false;
            }));

            if ($validated->count() == 0) {
                $parameters = collect($parameters);

                $parameters->except($validated->keys());

                throw new TypeError('Invalid Parameters:' . $parameters->toJson());
            }
        }

        return;
    }
}
