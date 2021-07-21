## Mapper Version 2.x

> Version 2.4.8

## 기존 방식

- Mapper: 테이블과 동일한 속성을 가지고 DTO와 Model을 연결 해주는 역할을 담당

> [Mapper](https://github.com/miniyus/tongdocAPI/tree/0.9.x/app/Mappers/Mapper.php)

> [DTO](https://github.com/miniyus/tongdocAPI/tree/0.9.x/app/DataTransferObjects/Dto.php)

_Demo_

> Demo: Mapper

```php
<?php

namespace App\Mappers\V1;

use App\Mappers\Mapper;
use App\Data\DataTransferObjects\DemoDto;
use App\Libraries\Data\Dto;
use Hash;
use App\Models\Demo;

class DemoMapper extends Mapper
{
    protected $property;

    public function model(array $attributes= []): Demo
    {
        return new Demo;
    }

    public function getDtoObject()
    {
        return new DemoDto();
    }

    /**
     * dto와 모델 속성 매핑
     *
     * @param   Dto  $user  [$user description]
     *
     * @return  [type]          [return description]
     */
    public function mappingDto(Dto $dto): DemoMapper
    {
        return $this->mappingDemoDto($dto);
    }

    /**
     * dto와 모델 속성 매핑
     * +타입 힌팅을 통하여 오류를 보다 쉽게 잡기 위해 만든 메서드
     *
     * @param   DemoDto  $user  [$user description]
     *
     * @return  [type]          [return description]
     */
    public function mappingDemoDto(DemoDto $demo): DemoMapper
    {
        $this->setProperty($demo->getProperty());

        return $this;
    }

    /**
     * Dto로 변환
     *
     * @return  DemoDto [return description]
     */
    public function toDto(): DemoDto
    {
        $dto = $this->getDtoObject();
        $dto->setProperty($this->getProperty());

        return $dto;
    }

    // getter and setter
    public function getProperty(){
        return $this->property;
    }

    public function setProperty($value){
        $this->property = $value;
        return $this;
    }
}

```

> Demo: Dto

```php
<?php

namespace App\Data\DataTransferObjects;

use App\Libraries\Data\Dto;

class DemoDto extends Dto
{
    protected $property;

    public function getProperty(){
       return $this->property;
    }

    public function setProperty($value){
        $this->property = $value;
        return $this;
    }

}

```

## 2.x

### 목적

라라벨 프레임워크는 유용한 프레임워크이지만, 기본적으로 연관배열의 사용을 권장합니다. 이는 생산성을 증가 시키는데 크게 기여하지만 협업 및 유지보수 관리의 어려움을 발생시킬 수 있습니다. 생산성에 영향을 최대한
줄이면서 유지보수 관리 또한 쉽게하기 위해 view를 위한 DTO와 DB 데이터 관리를 위한 Entity객체로 분할하려고 합니다. 하지만 DTO의 경우는 사용자에게 보여주기 위한 객체이고 Entity는 DB테이블
스키마를 그대로 가진 객체이기 때문에, 서로 속성간의 차이가 발생할 가능성이 생기고 DTO는 상황에 따라서 변경될 수 있기 때문에 둘 사이를 mapping해주는 객체가 필요하다고 판단하여 현재 mapper를
구상했습니다.

### 구조

- **DTO(영향가능성 낮음)**:
  기존 형식을 최대한 유지, Dto를 새로 생성하였으며, class에 명명해 놓지 않은 새로운 public속성을 임의로 만들지 못하게 차단했습니다. 그리고 toJson 메서드, newInstance() 메서드를
  추가했습니다.
  <br>

- **Mapper(영향가능성 높음)**:
  repository당 1개의 Mapper를 가졌지만 Mapper는 이제 하나의 클래스로 존재 합니다. 기존의 작동방식과 차이가 생겼습니다. Interface로 'mappingDto','mappingEntity'
  메서드에 접근하여, 변환을 할 수 있습니다. 또한 Dto혹은 Entity타입에 따라서 config/mapper.php파일을 이용해 dto,entity,map 클래스를 연결합니다. 그리고 mapper는 이제
  Service계층에서 사용하며, repository 계층은 항상 input과 return을 Entity객체로 고정합니다. 그 대신 repository의 로직을 간소화(DB관련 데이터 처리만)합니다.
  (Model과 Entity 사이의 변환은 Entity클래스의 'toModel()'메서드와 'map()'메서드로 처리 가능합니다.)

<br>

**새로 추가 된 클래스**

- **Entity(영향가능성 높음)**:
  기존의 Mapper는 Mapper자신이 DB와 연관된 속성을 가졌지만, 그것을 분리하여 데이터는 Enity객체가 가지고 Mapper는 DTO와 Entity을 연결해주는 역할을 합니다. Mapper와
  Repository메서드의 input과 return에 사용됩니다.
  <br><br>

- **Entities(영향가능성 높음)**:
  라라벨 모델에서 여러개의 레코드를 가져올 경우 라라벨 컬렉션을 상속받은 Eloquent Collection 객체를 사용하는 것을 보고 Entity객체를 컬렉션객체를 통해 컨트롤하면 좋겠다고 판단하여, 라라벨
  컬렉션을 상속받은 Entities클래스를 구현했습니다. 기본 컬렉션과 다른 점은 Entities::toDtos() Entities와 같은 목적의 Dto컬렉션으로 변환하는 메서드가 추가되었습니다.
  <br><br>

- **Dtos(영향가능성 높음)**:
  Entities와 같은 목적으로 설계하여 Dto객체를 보다 쉽게 제어할 수 있습니다.
  <br><br>

- **Map(영향가능성 높음)**:
  기존의 Mapper 메인기능(mappingDto,mappingModel)들을 Map이라는 클래스에게 넘겼습니다. Interface와 Abstract를 적극 사용하여, 생산성 향상을 기대합니다. Map은 최소
  Repository당 한개씩 새로 필요합니다. 대신 구현할 method는 2개로 toEntity()와 toDto()입니다.<br>
  **Map함수를 구현하지 않는다면 dto,entity의 map()메서드로 매핑가능(단, 속성이 일치해야 원하는 결과를 얻을 수있습니다.)**
  <br><br>

**추가로 toEntity()와 toDto() 구현 시, getter,setter를 사용하면서 PHP docblock으로 getter 및 setter의 유효성을 체크하는 것을 권장합니다. 아니면 '
instanceof'를 사용하여도 무관 합니다. 현재 mapper2.0의 구현 목적 중 하나로 개발과정에서 발생할 수 있는 에러를 IDE를 통해 미리 방지하는 것도 포함되어 있기 때문입니다.**
<br>
- **MapperConfig(영향가능성 보통)**:
  config/mapper.php에서 원하는 값을 보다 쉽게 가져올 수 있게 해주는 클래스입니다. 더 이상 getIdentifier() 메서드를 구현할 필요가 없습니다.
  <br><br>
- **~~AutoMap~~(더 이상 사용하지 않습니다)**:
  > 2021.03.26: **auto map은 더 이상 사용하지 않습니다.** 기존 {Dto|Entity}->map() 메서드로 대체합니다.<br>
  > 2021.03.08: auto map의 toDto(), toEntity() 메서드는 이제 JsonMapper 라이브러리를 사용합니다.<br>

~~Map클래스가 항상 필요하지만, 만약 DTO와 Entity 속성이 일치할 경우~~
~~자동으로 Mapping해줄 수 있게 AutoMap클래스를 생성하였습니다.~~
~~만약에 config/mapper.php Dto,Model,Entity만 정의해 놓는다면~~
~~entity->toDto() 혹은 dto->toEntity() 메서드를 통해 자동으로 매핑됩니다.~~
~~일치하지 않는 경우 TypeErrorException을 던집니다.~~

- **DataMapper(영향가능성 낮음)**:
  기존에 내장되어 있던 map(), mapList() 메서드의 로직을 다른 공통의 클래스로 분리 하였습니다.
  기존 코드에 영향 없이 map(), mapList() 메서드를 사용할 수 있습니다.
  
- **Dynamic(영향가능성 낮음)**:
  이는 새로 추가된 데이터 객체 유형입니다. 라라벨 Model 클래스를 모방하여 만들었습니다.
  속성들을 매직 메서드를 활용하여 제어 합니다.(테스트 혹은 속성이 자주 변할 수 있는 경우에 사용)

- **CustomCollection(영향가능성 낮음)**
  Dtos, Entities 의 공통 기능을 통합하기 위해서 해당 클래스를 상속 받습니다.
  
- **Traits(영향가능성 낮음)**:
  공통 기능들을 가능한대로 Trait 을 활용하여 적용 시켰습니다.
  - ReadOnlyDto: 읽기 전용 DTO
  - ToDto: toDto() 메서드 구현
  - ToDtos: toDtos() 메서드 구현
  - ToEntities: toEntities() 메서드 구현
  - ToEntity: toEntity() 메서드 구현
  - Transformation: toArray(), toJson(), makeHidden(), makeVisible(), jsonSerialize() 구현

- **Mapable(영향가능성 보통)**:
  map(), mapList(), toArray()(Arrayable 상속), toJson()(Jsonable 상속) 메서드를 가진 interface
  Dynamic 클래스의 추가하면서 map(), mapList(), toArray() 와 같은 Data 객체들의 공통 기능들을 묶을 interface가 필요하다고 판단하여 추가하였습니다.
  
- **추가 변경사항**

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
$demo= DemoEntity::newInstance()->initialize();

```

> 2021.06.17<br>
> 클로저 뿐만 아니라, callable 함수도 적용이 가능합니다.<br>
> 콜백 파라미터에 Map 클래스의 이름(Map::class)을 보내면, config 파일에 명시 하지 않았더라도 Mapping 할 수 있습니다.(테스트에서 유용할 수 있습니다.)
```php
<?php
// callable example
class CallableDemo{
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

### CODE

- **Config**

    - [config/mapper](./config/mapper.php)
    - [MapperConfig](./app/Libraries/MapperV2/MapperConfig.php)

- **Mapper**

    - [Mapper Interface](./app/Libraries/MapperV2/MapperInterface.php)
    - [Mapper](./app/Libraries/MapperV2/Mapper.php)

- **Map**
    - [Map Interface](./app/Libraries/MapperV2/Maps/MapInterface.php)
    - [Map](./app/Libraries/MapperV2/Maps/Map.php)
    - [~~AutoMap~~](./app/Libraries/MapperV2/AutoMap.php)
- **Entity**

    - [~~Entity Interface~~](app/Libraries/Data/Contracts/Entity/Entity.php)
    - [Entity](app/Libraries/Data/Entity.php)
    - [Entities](app/Libraries/Data/Entities.php)

- **Dto**
    - [~~Dto Interface~~](app/Libraries/Data/Contracts/DataTransferObject/Dto.php)
    - [Dto](app/Libraries/Data/Dto.php)
    - [Dtos](app/Libraries/Data/Dtos.php)
