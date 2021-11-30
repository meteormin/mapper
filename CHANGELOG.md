## Change Log

**last update: 2021.11.30**

### 2021.11.30: <br>

> v2.6.5

**feat**

Transformaion Trait의 initialize() 메서드 관련

- Property 클래스의 fillDefault() 메서드 수정
    - NULL 허용 속성은 기본 값 할당 시 NULL 할당

### 2021.11.19: <br>

> v2.6.4

**feat**

객체지향 프로그래밍: GenerateMap Artisan 명령 클래스들 구조

- 생성자 및 메서드간의 인수전달을 객체로 하여 객체지향적으로 구현
- 의존성 주입도 가능하게 끔?

Map.stub 및 파라미터 수정

- 기존 형식은 dto, entity의 풀네임으로 작성되어 가독성이 떨어졌다.
- use 문을 제외한 코드는 풀네임이 아닌 클래스명으로 수정

**docs**

CHANGELOG 형식 변경...

README에서 CHANGELOG 분리




> 2021.10.13
>
> v2.6.3
>
> Dynamic __get(), __set() 매직 메서드 수정
> __get(), __set() 매직 메서드내부에서 getter, setter 호출을 하는데 매직 메서드를 기준으로 작동하게 만들어져 있어 임의의 getter, setter를 작성하면, Dynamic 클래스 의도대로 작동하지 않는 문제 발생
>
> __get(), __set() 메서드의 경우도 __call 메서드 처럼 getAttribute(), setAttribute()를 호출하게 수정

> 2021.09.01
>
> v2.6.2
>
> CustomCollection makeHidden() 메서드가 여전히 정상 작동하지 않는다.
>> 버그 원인을 잘못 파악했었다. <br>
> > 원인은 '...' 키워드를 이용한 함수의 가변 인자의 활용에서 문제가 발생했다.
> > https://github.com/miniyus/mapper/issues/1

```php
<?php
function first(...$args){
    second($args);
}

function second(...$args){
    return $args;
}

first([...]); // 배열을 인자로 넣으면 second는 2차원 배열을 반환하게 된다.

// 설계 의도대로 적용하려면 배열 검사를 해줘야한다..
function first(...$args){
    second(is_array($args[0]) ? $args[0] : $args);
}

function second(...$args){
    return $args;
}

```

> 기타 docblock 수정
>
> Dtos의 생성자 오류 수정

```php
    /**
     * @param array|Arrayable|ArrayAccess|null $dtos
     */
    public function __construct($dtos = [])
    {
        // ArrayAccess 인스턴스 비교가 중복되어 처리되고 있었다.
        if (is_array($dtos) || ($dtos instanceof ArrayAccess) || ($dtos instanceof ArrayAccess)) {
            parent::__construct($dtos);
        }
    }
    
    // 수정 후
    /**
     * @param array|Arrayable|ArrayAccess|null $dtos
     */
    public function __construct($dtos = [])
    {
        // Entities와 동일하게 Arrayable로 수정
        if (is_array($dtos) || ($dtos instanceof Arrayable) || ($dtos instanceof ArrayAccess)) {
            parent::__construct($dtos);
        }
    }
```

> 2021.08.26
>
> v2.6.1
>
> 리플렉션 관련 사항 수정(기존 코드에는 딱히 영향 없음)
>> 이제 getter, setter가 구현되어 있지 않더라도 값을 제어할 수 있음
> > 여전히 toArray() 메서드는 getter를 기준으로 결과를 출력<br>
>
> DataMapper::map() target class type object
>
> 기타 타입 관련 주석 수정
>
> Entity::toArray()의 allowNull 파라미터는 항상 true
>
> CustomCollection::makeHidden() 메서드 버그 수정
>> Colleciton::map() 메서드의 결과는 기존 index가 연관배열이 아닌 일반 배열일 경우
> > index가 string으로 변환되기 때문에 일반배열로 map()메서드를 사용했으면, values()->all() 메서드까지 실행시켜줘야한다.

> 2021.08.25
>
> v2.6.0
> 현재 REAME의 사용법대로 사용할 경우, 크게 변동되는 부분은 없으며
> 별도로 Mapper 클래스 생성 혹은 정적 메서드로 사용하고 있는 경우는
> 소스코드의 수정이 필요합니다.
>
> CustomCollection 수정
>
>> 기존 toJson() 제거
>>
>> Transformation Trait 사용<br>
> > makeVisible(), makeHidden() 메서드 collection 객체에 맞춰 확장
>
> Mapper 및 MapperInterface 수정<br>
>> mappingDto() 및 mappingEntity() 메서드와 생성자 로직 제거<br>
> > mappingDto(), mappingEntity() 메서드 대신, map(),mapList() 메서드 추가<br>
> > toEntities() 및 toDtos() 메서드 제거<br>
> > mapping 우선순위 변경, 1.메서드 파라미터 > 2.config/mapper 설정
>
> DataMapper 수정<br>
>> map() 메서드 수정, 콜백의 리턴 유형이 배열일 경우도 매핑이 가능

```php
<?php
// 배열 리턴 예시
$dto->map($data,function($data){
    return [
       'property_name'=>$data['key']
    ];
});
```

> Dto, Entity phpdoc 주석, 파라미터 및 리턴 타입 정리
>
> Dynamic, Mapable 클래스의 toArray(),toJson()의 allowNull 관련(v2.5.8) 수정사항 반영
>
> ToDto, ToEntity, ToDtos, ToEntities Trait 수정된 Mapper에 맞춰 수정<br>
> 기존의 메서드들은 Mapper 클래스의 mapping{Dto|Entity} 메서드를 사용하였지만
> 수정사항에 맞게 단일 객체 클래스들은 map() 메서드, 컬렉션 객체 클래스들은 mapList() 메서드를 사용하게 수정

> 2021.08.20<br>
> v2.5.8<br>
> Transformation Trait 수정<br>
> toJson() allowNull 파라미터 제거<br>
> makeHidden(), makeVisible() 작동 방식 변경

```php
<?php
    /**
     * @param int|string $options
     * @return string
     * @version 2.5.8 allowNull 파라미터 제거
     */
    public function toJson($options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT): string
    {
        return json_encode($this, $options);
    }

    /**
     * makeHidden
     * toArray() 메서드 작동 시, 숨기고 싶은 속성을 정할 수 있다
     * @param string|array|null $hidden
     * @return $this
     * @version 2.5.8 작동 방식 변경
     */
    public function makeHidden(...$hidden): self
    {
        $this->hidden = array_merge(
            $this->hidden, is_array($hidden[0]) ? $hidden[0] : $hidden
        );

        return $this;
    }

    /**
     * toArray() 메서드 작동 시, 숨겼던 속성 항목을 다시 출력 시킬 수 있다.
     * @param string|array|null $visible
     * @return $this
     * @version 2.5.8 작동 방식 변경
     */
    public function makeVisible(...$visible): self
    {
        $this->hidden = array_diff(
            $this->hidden, is_array($visible[0]) ? $visible[0] : $visible
        );
        return $this;
    }
```

> Mapper 수정<br>
> toDto(),toEntity() $callback 파라미터 is_callable, is_string 검사 순서 변경<br>
> 예외 처리 TypeError에서 InvailidArgumentException으로 수정

```php
<?php
    /**
     * mapper에 데이터를 dto형식에 맞게 변환
     * Entity -> Dto
     *
     * @param Entity $entity
     * @param Dto|null $dto
     * @param Closure|callable|string|null $callback
     * @return  Dto  [return description]
     * @throws JsonMapper_Exception
     *  @version 2.5.8 callable 검사와 string 검사 순서 변경
     */
    protected function toDto(Entity $entity, ?Dto $dto, $callback = null): Dto
    {
        if (is_null($entity->toArray())) {
            return $dto;
        }

        if (!is_null($dto) && !is_null($callback)) {
            if (is_callable($callback)) {
                $result = $callback($entity, $dto);
                if (!($dto instanceof Dto) || is_array($result)) {
                    $dto->map($result);
                }
            } else if (class_exists($callback)) {
                /** @var Map $map */
                $map = new $callback;
                if ($map instanceof Map) {
                    $dto = $map->entityToDto($entity, $dto);
                } else {
                    throw new InvalidArgumentException(get_class($map) . ': 콜백 클래스는 Map 클래스를 상속받은 클래스이여야 합니다.');
                }
            } else {
                throw new InvalidArgumentException(get_class($entity) . ': Dto변환 실패 $callback파라미터가 올바르지 않습니다.');
            }
        } else if (!is_null($this->map)) {
            $dto = $this->map->entityToDto($entity, $dto);
        } else if (!is_null($dto)) {
            $dto->map($entity);
        } else {
            throw new InvalidArgumentException(get_class($entity) . ': Dto변환 실패 Dto객체가 null입니다.');
        }

        return $dto;
    }

    /**
     * Dto -> Entity
     * @param Dto $dto
     * @param Entity|null $entity
     * @param Closure|callable|string|null $callback
     * @return Entity
     * @throws JsonMapper_Exception
     * @version 2.5.8 callable 검사와 string 검사 순서 변경
     */
    protected function toEntity(Dto $dto, ?Entity $entity, $callback = null): Entity
    {
        if (is_null($dto->toArray())) {
            return $entity;
        }

        if (!is_null($entity) && !is_null($callback)) {
            if (is_callable($callback)) {
                $result = $callback($dto, $entity);
                if (!($entity instanceof Entity) || is_array($result)) {
                    $entity->map($result);
                }
            } else if (is_string($callback)) {
                /** @var Map $map */
                $map = new $callback;
                $entity = $map->dtoToEntity($dto, $entity);
            }
        } else if (!is_null($this->map)) {
            $entity = $this->map->dtoToEntity($dto, $entity);
        } else if (!is_null($entity)) {
            $entity->map($dto);
        } else {
            throw new InvalidArgumentException(get_class($entity) . ': Entity변환 실패 Entity객체가 null입니다.');
        }

        return $entity;
    }
```

> 2021.08.06<br>
> v2.5.7<br>
> v2.5.6 변경사항 제거

> 2021.08.06<br>
> v2.5.6<br>
> Reflection class를 다루는 Property 클래스의 toArray() 메서드 개선
> 속성이 객체인 경우 Property() 객체를 새로 생성하여 toArray() 메서드를 실행시켜 중첩객체까지 배열로 리턴할 수 있게 개선

```php
/**
 * to array
 *
 * @return array|null
 */
public function toArray(): ?array
{
    $arr = [];
    foreach ($this->properties as $prop) {
        if (method_exists($this->origin, 'get' . Str::studly($prop->getName()))) {
            if ($prop->isInitialized($this->origin)) {
                $arr[$prop->getName()] = $this->origin->{'get' . Str::studly($prop->getName())}();
                // update 2021.08.06 v2.5.6
                if (is_object($arr[$prop->getName()])) {
                    $arr[$prop->getName()] = (new static($arr[$prop->getName()]))->toArray();
                }
            }
        }
  
    return $arr;
}
```

> 2021.08.05<br>
> v2.5.5<br>
> Transformation Trait toArray() 메서드 기본 값 설정<br>
> 기존에는 코드에 강제로 allowNull은 false, 배열 키의 case_style은 snake_case로 고정했었는데 이 부분을 config파일을 통해서 수정할 수 있게 하였습니다.

```php
/** config/mapper.php **/
return [
    'transformation' => [
        /**
         * allow null: boolean(default: false)
         */
        'allow_null' => false,

        /**
         * case style: snake_case or camel_case
         */
        'case_style' => 'snake_case',
    ],
    ...
];

/** Transformation Trait toArray 메서드 일부 */
if (config('mapper.transformation.case_style') == 'camel_case') {
    $attributes = ArrController::camelFromArray($property->toArray());
} else {
    $attributes = ArrController::snakeFromArray($property->toArray());
}

if (is_null($allowNull)) {
    $allowNull = config('mapper.transformation.allow_null');
}
       
if (!$allowNull) {
    $attributes = ArrController::exceptNull($attributes);
}
```

> 2021.07.21<br>
> Utils Property 클래스 기능 추가
> fillDefault()
> getDefaultValue()
>
> Transformation Trait 기능 추가
> initialize()
> 할당되지 않은 속성에 타입 유형에 맞는 기본값으로 초기화 할 수 있습니다.
>

```php
<?php
// initialize 메서드로 할당되지 않은 속성에 타입유형에 맞게 기본값으로 초기화
$demo = DemoEntity::newInstance()->initialize();

```

> 2021.06.17<br>
> 클로저 뿐만 아니라, callable 함수도 적용이 가능합니다.<br>
> 콜백 파라미터에 Map 클래스의 이름(Map::class)을 보내면, config 파일에 명시 하지 않았더라도 Mapping 할 수 있습니다.

```php
<?php
// callable example
class CallableDemo
{
    /**
     * @param Dto $dto
     * @param Entity $entity 
     * @return string
     */
    public function callback(Dto $dto, Entity $entity): string
    {
        $entity->setProperty($dto->getProperty());
        return $entity;
    }
    
    public function example()
    {
        $dto = new DemoDto;
        return $dto->toEntity(DemoEntity::class, [$this, 'callback']);
    }
}

// class name 활용
$dto = new DemoDto;
$dto->toEntity(DemoEntity::class, DemoMap::class);
```

> 2021.05.21<br>
> Map클래스를 구현하지 않더라도 Mapping이 가능합니다.<br>
> 클로저를 이용하여 Mapping 가능<br>

```php
<?php
    // 새로운 사용법
    // 1. map클래스 없이 사용가능
    $entity = new Entity;
    $entity->toDto(Dto::class); // mapping할 Dto클래스 설정
//  $dto = Dto::newInstance($entity); 이 코드와 동일하다
    // 2. 클로저 이용
    // 1번과 마찬가지로 mapping할 Dto클래스를 설정해줘야한다.
    // 클로저의 첫번째 파라미터는 데이터 객체, 두번째 파라미터는 매핑되는 결과 객체
    $dto = $entity->toDto(Dto::class, function($entity,$dto){ 
        // mapping
        return $dto->map($entity);
    });
//  $dto = Dto::newInstance($entity); 이 코드와 동일하다.

    // 1번 방법의 경우 직접 Map()메서드를 사용 하는 것과 크게 다르지 않다.
    // 2번 방법의 경우,map클래스의 기능을 클로저를 통해 수행할 수 있게 된다.
     $dto = $entity->toDto(Dto::class, function($entity,$dto){ 
        $dto->setId($entity->getId());
        return $dto;
    });
```

> 2021.03.22<br>
> 이제 Mapper클래스는 각 데이터 클래스에 내장되었습니다.<br>

```php
<?php
    // entity to dto
    $entity = new Entity;
    $entity->toDto();

     // dto to entity
    $dto = new Dto;
    $dto->toEntity();

    // entities to dtos
    $entities = new Entities;
    $entities->toDtos();

    // dtos to entities
    $dtos = new Dtos;
    $dtos->toEntities();
```
