<?php

use Miniyus\Mapper\Exceptions\DtoErrorException;
use Miniyus\Mapper\Exceptions\EntityErrorException;

require_once './tests/Data/DemoDto.php';
require_once './tests/Data/DemoEntity.php';
require_once './tests/Data/DemoMap.php';

class MapperTest extends \PHPUnit\Framework\TestCase
{
    private \Miniyus\Mapper\Mapper $mapper;

    public function setUp(): void
    {
        parent::setUp();
        $this->mapper = \Miniyus\Mapper\MapperFactory::make();
    }

    /**
     * @return void
     * @throws JsonMapper_Exception
     * @throws DtoErrorException
     * @throws EntityErrorException
     */
    public function testMapping()
    {
        $input = [
            'id' => 1,
            'name' => 'DTO'
        ];

        $dto = new \Data\DemoDto($input);

        $rsEntity = $this->mapper->map($dto,
            \Data\DemoEntity::class,
            \Data\DemoMap::class
        );


        $validateEntity = \Data\DemoEntity::newInstance([
            'id' => 1,
            'name' => 'DTO'
        ]);

        $this->assertEquals($rsEntity, $validateEntity);
    }
}