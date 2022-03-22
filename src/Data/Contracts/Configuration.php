<?php

namespace Miniyus\Mapper\Data\Contracts;

interface Configuration
{
    /**
     * @return array
     */
    public function all(): array;

    /**
     * @param int|string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * @param array|string $keys
     * @return bool
     */
    public function has($keys): bool;

    /**
     * @param callable $callback
     * @return array
     */
    public function filter(callable $callback): array;
}