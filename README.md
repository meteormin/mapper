## Mapper with Dto and Entity

 <img alt="Version" src="https://img.shields.io/badge/version-2.7.0-blue.svg?cacheSeconds=2592000" />
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
// Closure 혹은 기타 callable을 활용할 경우 해당 로직으로 완전히 대제됩니다.
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
// 콜백 파라미터가 있으면 먼저 일치하는 항목들을 매핑하고 콜백 함수의 내용을 실행
\Miniyus\Mapper\Data\DataMapper::map($data, $object, $callback);
```

</details>

## [Change Log](CHANGELOG.md)