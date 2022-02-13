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
 *
 * @template T
 */
class DataMapper
{
    /**
     * 콜백이 있는 경우, 먼저 일치하는 속성들을 매칭 후 콜백함수를 실행합니다.
     * callback 규칙: function({매핑 데이터}, {매핑 객체})
     * @param array|Arrayable|Jsonable|Mapable $data
     * @param T $object
     * @param callable|Closure|null $callback
     * @return T
     * @throws JsonMapper_Exception
     * @version 2.6.7
     */
    public static function map($data, $object, $callback = null)
    {
        $jsonMapper = new JsonMapper();

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
        } else if ($data instanceof ArrayAccess) {
            $json = json_decode(json_encode($data));
            if (is_object($json)) {
                $object = $jsonMapper->map($json, $object);
            }
        } else if ($data instanceof Jsonable) {
            $json = json_decode($data->toJson());
            if (is_object($json)) {
                $object = $jsonMapper->map($json, $object);
            }
        } else if (is_array($data)) {
            $json = json_decode(json_encode($data));
            $object = $jsonMapper->map($json, $object);
        } else {
            $object = $jsonMapper->map($data, $object);
        }

        if (!is_null($callback)) {
            $object = $callback($data, $object);
            if (is_array($object)) {
                $json = json_decode(json_encode($data));
                if (is_object($json)) {
                    $object = $jsonMapper->map($json, $object);
                }
            }
        }

        return $object;
    }

    /**
     * @param Collection<mixed,mixed>|array $data
     * @param T $object
     * @param Closure|callable|null $callback
     * @return Collection<mixed,T>
     * @throws JsonMapper_Exception
     */
    public static function mapList($data, $object, $callback = null): Collection
    {
        if (!is_array($data) && !($data instanceof Collection)) {
            throw new TypeError(get_class($object) . '은 매핑할 수 없습니다.');
        }

        $rsList = collect();
        $class = get_class($object);
        foreach ($data as $value) {
            $rsList->add(self::map($value, new $class, $callback));
        }

        return $rsList;
    }
}
