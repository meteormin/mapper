## Mapper with Dto and Entity

 <img alt="Version" src="https://img.shields.io/badge/version-2.6.3-blue.svg?cacheSeconds=2592000" />
  <a href="https://php.net" target="_blank">
    <img src="https://img.shields.io/badge/php-%5E7.4.0-blue" alt=""/>
  </a>
  <a href="https://laravel.com" target="_blank">
    <img src="https://img.shields.io/badge/laravel-7.x-lightgrey" alt=""/>
  </a>
  <a href="https://github.com/miniyus/tongdocAPI#readme" target="_blank">
    <img alt="Documentation" src="https://img.shields.io/badge/documentation-yes-brightgreen.svg" />
  </a>
  <a href="https://github.com/miniyus/tongdocAPI/graphs/commit-activity" target="_blank">
    <img alt="Maintenance" src="https://img.shields.io/badge/Maintained%3F-yes-green.svg" />
  </a>
  <a href="https://github.com/miniyus/tongdocAPI/blob/master/LICENSE" target="_blank">
    <img alt="License: MIT" src="https://img.shields.io/badge/license-MIT-yellow" />
  </a>

## 목차

- [목적](#목적)
- [구조 및 용도](#구조-및-용도)
- [설치](#설치)
- [사용법](#사용법)
- [changelog](#change-log)

## 목적

라라벨 프레임워크는 유용한 프레임워크이지만, 기본적으로 연관배열의 사용을 권장합니다. 이는 생산성을 증가 시키는데 크게 기여하지만 협업 및 유지보수 관리의 어려움을 발생시킬 수 있습니다. 생산성에 영향을 최대한
줄이면서 유지보수 관리 또한 쉽게하기 위해 view를 위한 DTO와 DB 데이터 관리를 위한 Entity객체로 분할하려고 합니다. 하지만 DTO의 경우는 계층간 데이터 교환을 위한 객체이고 Entity는 DB테이블
스키마를 그대로 가진 객체이기 때문에 서로 속성간의 차이가 발생할 수 있으며, Entity에 비해 상대적으로 변경사항이 많을 수 있기 때문에 두 클래스간의 변환 및 제어를 해주는 기능이 필요하다고 판단하여 현재
패키지를 구상했습니다.

## 구조 및 용도

- **DTO**
    - DataTransferObject로 데이터 전달의 역할을 수행합니다. Request에서 받은 데이터를 다른 계층간 데이터 전달 용도입니다.
      <br><br>

- **Entity**
    - DB의 테이블의 속성을 그대로 갖는 클래스입니다.
      <br><br>

- **Mapper**
    - Dto혹은 Entity타입에 따라서 config/mapper.php파일을 이용해 dto,entity,map 클래스를 연결합니다.
    - Mapper는 따로 호출할 수 있지만, DTO, Entity에 내장되어 toDto(), toEntity() 메서드를 통해 호출이 가능합니다.
      <br><br>

- **Entities**
    - 라라벨 모델에서 여러개의 레코드를 가져올 경우 라라벨 컬렉션을 상속받은 Eloquent Collection 객체를 사용하는 것을 보고 Entity객체를 컬렉션객체를 통해 컨트롤하면 좋겠다고 판단하여,
      라라벨 컬렉션을 상속받은 Entities클래스를 구현했습니다. 기본 컬렉션과 다른 점은 Entities::toDtos() Entities와 같은 목적의 Dto컬렉션으로 변환하는 메서드가 추가되었습니다.
      <br><br>

- **Dtos**
    - Entities와 같은 목적으로 설계하여 Dto객체를 보다 쉽게 제어할 수 있습니다.
      <br><br>

- **Map**
    - Dto와 Entity간의 변환시 각 속성들을 매핑해 줄 수 있는 클래스입니다.
    - 구현할 method는 2개로 toEntity()와 toDto()입니다.<br>
    - Dto와 Entity의 속성명과 유형이 모두 일치하지 않을 경우, 서로 다른 속성의 매칭이 필요할 때 사용됩니다.

    - **Map클래스를 구현하지 않을 경우 Dto, Entity의 map() 메서드를 사용하게 됩니다.(단, 속성이 일치해야 원하는 결과를 얻을 수있습니다.)**
    - **toEntity()와 toDto() 구현 시, getter,setter를 사용하면서 PHP docblock으로 getter 및 setter의 유효성을 체크하는 것을 권장합니다. 아니면 '
      instanceof'를 사용하여도 무관 합니다. 현재 mapper2.0의 구현 목적 중 하나로 개발과정에서 발생할 수 있는 에러를 IDE를 통해 미리 방지하는 것도 포함되어 있기 때문입니다.**
      <br>
      <br>

- **MapperConfig**
    - config/mapper.php에서 원하는 값을 보다 쉽게 가져올 수 있게 해주는 클래스입니다.
      <br>
      <br>
- **DataMapper**
    - Dto, Entity 클래스의 map(), mapList() 메서드 내부에서 호출되는 클래스입니다.
    - JsonMapper 라이브러리를 사용합니다.
      <br>
      <br>
- **Dynamic**
    - 동적 속성할당 클래스입니다. $fillable 속성에 문자열 배열로 사용할 속성을 정의하여 사용합니다.
    - 라라벨 Model 클래스를 모방하여 만들었습니다. 속성들을 매직 메서드를 활용하여 제어 합니다.(테스트 혹은 속성이 자주 변할 수 있는 경우에 사용)
      <br>
      <br>
- **CustomCollection**
  Dtos, Entities 의 공통 기능을 통합하기 위해서 해당 클래스를 상속 받습니다.
  <br>
  <br>
- **Traits**:
  공통 기능들을 가능한대로 Trait 을 활용하여 적용 시켰습니다.
    - ReadOnlyDto: 읽기 전용 DTO
    - ToDto: toDto() 메서드 구현
    - ToDtos: toDtos() 메서드 구현
    - ToEntities: toEntities() 메서드 구현
    - ToEntity: toEntity() 메서드 구현
    - Transformation: toArray(), toJson(), makeHidden(), makeVisible(), jsonSerialize() 구현
      <br>
      <br>
- **Mapable**
    - map(), mapList(), toArray()(Arrayable 상속), toJson()(Jsonable 상속) 메서드를 가진 interface Dynamic 클래스의 추가하면서 map(),
      mapList()
      , toArray() 와 같은 Data 객체들의 공통 기능들을 묶을 interface가 필요하다고 판단하여 추가하였습니다.
      <br><br>

## 설치

```shell
composer require miniyus/mapper

php artisan vendor:publish --provider="Miniyus\Mapper\Provider\MapperServiceProvider"
```

## 사용법

<details>
<summary>DTO</summary>

```php
<?php
// Dto 만들기
use Miniyus\Mapper\Data\Dto;

class DemoDto extends Dto
{
    private int $id;
    private string $name;
    
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): DemoDto
    {
        $this->id = $id;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
     /**
     * @param string $name
     * @return $this
     */
    public function setName($name): DemoDto
    {
        $this->name = $name;
        return $this;
    }
}

// 기능 예제

// 1. 인스턴스 생성
// 생성 시 파라미터는 Arrayable, array, object(public 속성만 할당 됨) 유형만 허용
/** @var DemoDto $dto */
$dto = new DemoDto(['id'=>1,'name'=>'abc']);
$dto = DemoDto::newInstance(['id'=>1,'name'=>'abc']);

// 2. map(), mapList() 메서드
// 생성 시 파라미터와 동일하다.
// 두번째 파라미터로 Closure, callable 활용이 가능하다.
$dto->map(['id'=>2,'name'=>'Laravel']);

// 콜백 파라미터 사용법
$dto->map($someArray,function($somArray, DemoDto $dto){
    return $dto->setId($somArray['id']);
});

// Dtos는 라라벨의 Collection 클래스를 상속받은 클래스
// 기본적인 사용법은 라라벨 Collection과 동일하다.
// 두번째 파라미터로 Closure, callable 활용이 가능하다.
/** @var \Miniyus\Mapper\Data\Dtos $dtos */
$dtos = $dto->mapList([DemoDto::newInstance(),DemoDto::newInstance()]);

// 3. Entity 변환
// 파라미터 없으면, config/mapper.php 파일에 매칭해둔 Map 클래스를 통하여 매핑한 Entity 객체를 반환
$dto->toEntity();

// 연결된 Map 클래스가 있으면 Map클래스의 toEntity() 메서드를 통해 매핑
// 연결된 Map 클래스가 없으면 (new DemoEntity())->map($dto) 와 같음
$dto->toEntity(DemoEntity::class);

// 2번째 파라미터 활용
// Map 클래스 지정 매핑
// Map 클래스를 config/mapper.php에 명시하지 않았더라도 매개변수로 넘겨주면 해당 Map클래스를 통해 매핑한다.
$dto->toEntity(DemoEntity::class, DemoMap::class);

// Closure 활용 매핑
$dto->toEntity(DemoEntity::class, function(DemoDto $dto, DemoEntity $entity){
    // getter, setter 매핑 로직
    return $entity;
});

// Closure 활용 매핑 2
$dto->toEntity(DemoEntity::class, function(DemoDto $dto){
    // Mapper는  배열로 반환된 경우도 매핑이 가능하다.
    return [
        'id'=>$dto->getId(),
        'name'=>$dto->getName()
    ];
});

// callable 활용
function exampleCallable($dto, $entity){
// getter, setter 등등... 매핑 로직
    return $entity;
}
$dto->toEntity(DemoEntity::class, 'exampleCallable');

// 4. 기타 변환

// 배열, 파라미터에서 null 허용 여부를 선택할 수 있다.
// true면 null인 속성도 출력, false면 null인 속성 제외 
$dto->toArray();

// json
// toJson 파라미터는 기존 toJson과 동일하게 option 파라미터가 들어간다.
$dto->toJson();

// 속성 숨김, laravel model의 hideAttributes의 makeHidden() 메서드와 사용법은 동일
$dto->makeHidden('name');

// 속성 보이기, laravel model의 hideAttributes의 makeVisible() 메서드와 사용법은 동일
$dto->makeVisible('name');

// 할당 되지 않은 속성 기본 값으로 초기화
$dto->initialize();

```

</details>

<details>
<summary>Entity</summary>

```php
<?php

use Miniyus\Mapper\Data\Entity;

/**
 * Class DemoEntity
 * 
 * @author Yoo Seongmin <miniyu97@iokcom.com>
 */
class DemoEntity extends Entity{
    // 기타 내장 메서드들은 toDto()를 제외하고 크게 다르지 않음
    // 구현하려는 설계 방식에 맞춰 작성
    
    /**
     * Model과 연결을 위해   
     * @return string
     */
    protected function getIdentifier() : string{
        return DemoModel::class;
    }
}

// 대부분의 기능들은 Dto와 동일
$entity = DemoEntity::newInstance();

// 사용법은 toEntity(),toEntities()와 동일하나, 첫번째 파라미터는 Dto를 상속받은 객체만 들어갈 수 있다.
$entity->toDto(DemoDto::class);
$entity->toDtos(DemoDto::class);

// 모델로 변환
$entity->toModel();
// 내부 적으로는 getIdentifier() 메서드의 명시한 모델을 생성하여 모델의 fill() 메서드를 활용한다.
(new DemoModel())->fill($entity->toArray(true));

```

</details>

<details>
<summary>Collections(Dtos,Entities)</summary>

```php
<?php
// 생성시, 파라미터는 array|Collection|Arrayable
$dtos = \Miniyus\Mapper\Data\Dtos::newInstance();
$entities = \Miniyus\Mapper\Data\Entities::newInstance();

// toDtos & toEntities()
// 입력받은 파라미터 클래스로 기존 데이터를 변환하고 Dtos,Entities 객체로 반환
// 사용법은 toDto(), toEntity() 와 같으나, 반환 결과는 Entities, Dtos
// 내부적으로 Mapper 클래스를 사용하기 때문에 두번째 파라미터의 사용법도 동일하다.
$entities->toDtos(DemoDto::class);
$dtos->toEntities(DemoEntity::class);

```

</details>

<details>
<summary>Map</summary>

```php
<?php

use Miniyus\Mapper\Maps\Map;

class DemoMap extends Map
{
    protected function toDto(\Miniyus\Mapper\Data\Entity $entity,\Miniyus\Mapper\Data\Dto $dto)
    {
        // TODO: Implement toDto() method.
        // case 1
        // getter, setter 활용
        if($entity instanceof DemoEntity && $dto instanceof DemoDto){
            // getter & setter
            return $dto;
        }
        
        // case 2
        // Map 클래스 또한 배열 리턴이 가능하다.
        return [
            'id' => $entity->getId();
        ];
    }
    
    protected function toEntity(\Miniyus\Mapper\Data\Dto $dto,\Miniyus\Mapper\Data\Entity $entity)
    {
        // TODO: Implement toEntity() method.
        // case 1
        if($entity instanceof DemoEntity && $dto instanceof DemoDto){
            // getter & setter
            return $entity;
        }
        
        // Map 클래스 또한 배열 리턴이 가능하다.
        return [
            'id' => $dto->getId();
        ];
    }
}
```

- generate:map 명령

```shell
# map 클래스는 php artisan generate:map {name} {--json=} 명령을 통해 생성할 수 있다.
# {name}은 생성할 Map 클래스이름, --json 옵션은 매핑관련 파일이다.
# config/mapper.php에 명시된 Map인 경우, 자동으로 생성해 준다.
# 단, Dto와 Entity객체에서 서로 일치하는 속성만 getter, setter를 만들어 준다.
# --json 옵션에 미리 어떤 항목끼리 매핑할지 정할 수 있다.
# 기타 경로 설정은 config/make_class.php 참조
php artisan generate:map DemoMap --json=DemoMap
```

    - generate:map --json={json filename} 파일 구조

```json
{
  "dto": "매핑하고자 하는 Dto 클래스의 이름(namespace 포함)",
  "entity": "매핑하고자 하는 Entity 클래스의 이름(namespace 포함)",
  "map": {
    "entityPropertyName": "dtoPropertyName"
  }
}
```

</details>

<details>
<summary>Dynamic</summary>

```php
use Miniyus\Mapper\Data\Dynamic;

class DemoDynamic extends Dynamic
{
    /**
     * 해당 속성의 배열 값이 해당 클래스에서 접근 및 제어 가능한 속성이 된다. 
     * @var array|string[] 
     */
    protected array $fillable = [
        'id',
        'name'
    ];
    
    /**
     * @param $data
     * @param callable|Closure|null $callback
     * @return \Miniyus\Mapper\Data\Contracts\Mapable
     */
    public function map($data,$callback = null) : \Miniyus\Mapper\Data\Contracts\Mapable
    {
        // TODO: Implement map() method.
    }
    
    /**
     * @param $data
     * @param callable|Closure|null $callback
     * @return Collection|array
     */
    public function mapList($data,$callback = null)
    {
        // TODO: Implement mapList() method.
    }   
}

// 1. 생성
// 생성자의 파라미터는 array 유형이다.
$demo = new Dynamic(['id'=>1,'name'=>'name']);

// Dynamic 클래스는 매직메서드를 사용하여 속성에 접근할 수 있다.
$demo->id = 1;
$demo->id;

// getter, setter처럼 사용할 수 있다.
$demo->setId(1);
$demo->getId();

// 2. 기타 변환
// Dto, Entity와 동일하게 toArray(), toJson()을 지원한다.
// 추가적으로 Dynamic클래스는 fromJson() 메서드를 사용할 수 있다.
$jsonString = "{\"id\":1,\"name\":\"name\"}";

$demo->fromJson($jsonString);

// fill(), 라라벨 Model의 fill()가 동일하다.(과정은 다르지만, 기능면으로)
$array = ['id'=>1,'name'=>'name'];
$demo->fill($array);

// 3. map(), mapList()
// Dynamic은 Mapable 인터페이스 메서드들을 구현해줘야 한다.
// 간단 예제
public function map($parameters)
{
    // 실제 fill() 메서드는 배열만 받기 때문에 예외 처리가 별도로 필요함.
    return $this->fill($parameters);
}

// mapList의 경우 명시적으로 return type이 정의되어 있지 않기 때문에 type에 주의
public function mapList($parameters)
{
    // collect 활용 예시
    return collect($parameters)->each(function($item){
        return (new static)->map($item);
    });
}
```

</details>

<details>
<summary>Mapper</summary> 

```php
<?php
// Mapper 클래스는 Entity, Dto에 내장되어 사용된다.
// Entity <-> Dto 변환에 특화되어 있기 때문에, 그 외의 용도로 사용할 수 없다.
// 객체 생성
$mapper = \Miniyus\Mapper\Mapper::newInstance();

// 단일 객체 매핑
$mapper->map($sourceObj, $targetClass, $callback);

// 리스트 객체 매핑 | array, Laravel Collection 객체 허용됨
$mapper->mapList($sourceList, $targetClass, $callback);

// 기타 정적 메서드 (제거: v2.6.0)
// \Miniyus\Mapper\Mapper::mappingDto($dto, $entityClassName, $callback);
// \Miniyus\Mapper\Mapper::mappingEntity($entity, $dtoClassName, $callback);


# DataMapper(JsonMapper를 Wrapping)
# JsonMapper에서 지원하지 않는 Type 지원 및 예외처리 로직을 추가했다.
# 배열 -> 객체, 객체 -> 객체 변환을 위한 클래스
// 첫번째 파라미터: 변환 전 데이터
// 두번째 파라미터: 데이터를 할당 받을 객체
// 세번째 파라미터: 콜백 함수
// 콜백 파라미터가 있으면 콜백 함수의 내용을 실행
// 콜백 함수가 없으면, JsonMapper::map() 기능과 동일하다
\Miniyus\Mapper\Data\DataMapper::map($data, $object, $callback);
```

</details>

## Change Log

<details>
<summary>last update: 2021.10.13</summary>

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

</details>