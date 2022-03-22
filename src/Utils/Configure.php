<?php

namespace Miniyus\Mapper\Utils;

use ArrayAccess;
use Illuminate\Support\Arr;
use Miniyus\Mapper\Data\Contracts\Configuration;

/**
 * 테스트용 or Laravel 환경이 아닐 때
 * config 내용 불러오기
 * Class Configure
 * @package Miniyus\Mapper\Utils
 * @author Yoo Seongmin <miniyu97@iokcom.com>
 */
class Configure implements Configuration
{
    private string $configPath;

    private string $configFilename;

    private array $config;

    public function __construct(string $configPath, string $filename)
    {
        $this->configPath = $configPath;
        $this->configFilename = $filename;

        $configFile = "$configPath/$filename.php";

        if (file_exists($configFile)) {
            $config = require $configFile;
            $this->config = is_array($config) ? $config : [];
        }
    }

    /**
     * @return string
     */
    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    /**
     * @return string
     */
    public function getConfigFilename(): string
    {
        return $this->configFilename;
    }

    public function all(): array
    {
        return $this->config;
    }

    /**
     * @param int|string|null $key
     * @param mixed|null $default
     * @return array|ArrayAccess|mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * @param array|string $keys
     * @return bool
     */
    public function has($keys): bool
    {
        return Arr::has($this->config, $keys);
    }

    /**
     * @param callable $callback
     * @return array
     */
    public function filter(callable $callback): array
    {
        return Arr::where($this->config, $callback);
    }
}