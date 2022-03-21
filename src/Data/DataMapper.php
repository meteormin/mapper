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
     * 콜백이 있는 경우, 먼저 일치하는 속성들을 매칭 후 콜백함수를 실행합니다.
     * callback 규칙: function({매핑 데이터}, {매핑 객체})
     * @param Arrayable|Mapable|Jsonable|array|ArrayAccess|object $data
     * @param object $object
     * @param callable|Closure|null $callback
     * @return object
     * @throws JsonMapper_Exception
     * @version 2.6.7
     */
    public static function map($data, object $object, $callback = null): object
    {
        $jsonMapper = new JsonMapper();
        $jsonObject = null;

        if ($data instanceof Mapable) {
            $jsonObject = json_decode(json_encode($data->toArray(true)));

        } else if ($data instanceof Arrayable) {
            $jsonObject = json_decode(json_encode($data->toArray()));

        } else if ($data instanceof ArrayAccess) {
            $jsonObject = json_decode(json_encode($data));

        } else if ($data instanceof Jsonable) {
            $jsonObject = json_decode($data->toJson());

        } else if (is_object($data) || is_array($data)) {
            $jsonObject = json_decode(json_encode($data));

        }

        if (is_object($jsonObject)) {
            $object = $jsonMapper->map($jsonObject, $object);
        }

        if (!is_null($callback)) {
            $object = $callback($data, $object);
            if (is_array($object)) {
                $jsonObject = json_decode(json_encode($data));
                if (is_object($jsonObject)) {
                    $object = $jsonMapper->map($jsonObject, $object);
                }
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
