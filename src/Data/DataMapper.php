<?php


namespace Miniyus\Mapper\Data;


use ArrayAccess;
use Miniyus\Mapper\Data\Contracts\Mapable;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonMapper;
use JsonMapper_Exception;
use TypeError;

/**
 * Class DataMapper
 * JsonMapper Wrapper
 * use JsonMapper
 * @package App\Libraries\Data
 */
class DataMapper
{
    /**
     * @param Arrayable|array|object $data
     * @param Arrayable $object
     * @param Closure|callable|null $callback
     * @return Arrayable
     * @throws JsonMapper_Exception
     */
    public static function map($data, Arrayable $object, $callback = null): Arrayable
    {
        if (!is_null($callback)) {
            return $callback($data, $object);
        }

        $jsonMapper = new JsonMapper();

        if ($data instanceof Mapable) {
            $json = json_decode(json_encode($data->toArray(true)));
            if (is_object($json)) {
                $object = $jsonMapper->map($json, $object);
            }
            return $object;
        }

        if ($data instanceof Arrayable) {
            $json = json_decode(json_encode($data->toArray()));
            if (is_object($json)) {
                $object = $jsonMapper->map($json, $object);
            }
            return $object;
        }

        if ($data instanceof Jsonable) {
            $json = json_decode($data->toJson());
            if (is_object($json)) {
                $object = $jsonMapper->map($json, $object);
            }
            return $object;
        }

        if (empty($data)) {
            return $object;
        }

        if (!is_object($data) && !is_array($data)) {
            return $object;
        }

        $json = json_decode(json_encode($data));
        if (is_object($json)) {
            $object = $jsonMapper->map($json, $object);
        }

        return $object;
    }

    /**
     * @param array|Arrayable|Collection $data
     * @param Arrayable $object
     * @param Closure|null $callback
     * @return Collection
     * @throws JsonMapper_Exception
     */
    public static function mapList($data, Arrayable $object, Closure $callback = null): Collection
    {
        if (!is_array($data) && !($data instanceof Arrayable) && !($data instanceof ArrayAccess) ) {
            throw new TypeError(get_class($object) . '은 매핑할 수 없습니다.');
        }

        $rsList = collect();
        $class = get_class($object);
        foreach ($data as $value) {
            $rsList->add(self::map($value, new $class, $callback));
        }

        return collect($rsList);
    }
}
