<?php

namespace Miniyus\Mapper\Utils;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * 라라벨 연관배열 키의 문자열 제어를 위해
 * 라라벨 Arr, Str 파사드에 의존
 */
class ArrController
{
    /**
     * snakeFromArray
     * 입력한 배열이 연관배열일 경우,
     * 배열의 키를 snake_case로 변환
     * @param array $array
     *
     * @return array
     */
    public static function snakeFromArray(array $array): array
    {
        if (!Arr::isAssoc($array)) {
            return $array;
        }

        $result = [];
        foreach ($array as $key => $value) {
            $result[Str::snake($key)] = $value;
        }

        return $result;
    }

    /**
     * camelFromArray
     * 입력한 배열이 연관배열일 경우,
     * 배열의 키를 CamelCase로 변환
     * @param array $array
     *
     * @return array
     */
    public static function camelFromArray(array $array): array
    {
        if (!Arr::isAssoc($array)) {
            return $array;
        }
        $result = [];
        foreach ($array as $key => $value) {
            $result[Str::camel($key)] = $value;
        }

        return $result;
    }

    /**
     * 값이 null인 데이터만 반환
     *
     * @param array $array
     *
     * @return array
     */
    public static function onlyNull(array $array): array
    {
        return Arr::where($array, function ($value) {
            return is_null($value);
        });
    }

    /**
     * 값이 null인 데이터 제외
     *
     * @param array $array
     *
     * @return array
     */
    public static function exceptNull(array $array): array
    {
        return Arr::where($array, function ($value) {
            return !is_null($value);
        });
    }
}
