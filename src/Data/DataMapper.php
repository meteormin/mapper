<?php


namespace Miniyus\Mapper\Data;


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
     * @param Arrayable|Mapable|Jsonable|array|object $data
     * @param object $object
     * @param Closure|callable|null $callback
     * @return object
     * @throws JsonMapper_Exception
     * @version 2.6.0 콜백의 return 유형이 array여도 mapping 가능하게 수정
     */
    public static function map($data, object $object, $callback = null): object
    {
        $jsonMapper = new JsonMapper();

        if (!is_null($callback)) {
            $result = $callback($data, $object);
            if (is_array($result)) {
                $json = json_decode(json_encode($data));
                if (is_object($json)) {
                    $result = $jsonMapper->map($json, $object);
                }
            }

            return $result;
        }

        if ($data instanceof Mapable) {
            $json = json_decode(json_encode($data->toArray(true)));
            if (is_object($json)) {
                $object = $jsonMapper->map($json, $object);
            }
        } else if ($data instanceof Arrayable) {
            $json = json_decode(json_encode($data->toArray()));
            if (is_object($json)) {
                $object = $jsonMapper->map($json, $object);
            }
        } else if ($data instanceof Jsonable) {
            $json = json_decode($data->toJson());
            if (is_object($json)) {
                $object = $jsonMapper->map($json, $object);
            }
        } else if (is_object($data) || is_array($data)) {
            $json = json_decode(json_encode($data));
            if (is_object($json)) {
                $object = $jsonMapper->map($json, $object);
            }
        }

        return $object;
    }

    /**
     * @param array|Collection $data
     * @param object $object
     * @param Closure|null $callback
     * @return Collection
     * @throws JsonMapper_Exception
     */
    public static function mapList($data, object $object, Closure $callback = null): Collection
    {
        if (!is_array($data) && !($data instanceof Collection)) {
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
