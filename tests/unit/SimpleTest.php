<?php

namespace Amsify42\Tests;

use PHPUnit\Framework\TestCase;
use Amsify42\TypeStruct\TypeStruct;

final class SimpleTest extends TestCase
{
    private $tsClass = \Amsify42\Tests\TypeStruct\Simple::class;
    private $arrData = [
                        'id'    => 42,
                        'name'  => 'amsify',
                        'email' => 'amsify@site.com',
                        'price' => 4.2
                    ];

    public function testArray()
    {
        $typeStruct = new TypeStruct();
        $typeStruct->setClass($this->tsClass);
        $result = $typeStruct->validate($this->arrData);
        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);
    }

    public function testObject()
    {
        $typeStruct = new TypeStruct();
        $typeStruct->isDataObject(true)->setClass($this->tsClass);
        $result = $typeStruct->validate($this->getStdClass());

        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);   
    }

    public function testHelper()
    {
        $result = get_typestruct($this->tsClass, 'class')->validate($this->arrData);

        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);   
    }

    public function testValidator()
    {
        $sample = new \Amsify42\Tests\Validators\Sample();
        $result = $sample->validate();
        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);
    }

    private function getStdClass()
    {
        $data        = new \stdClass();
        $data->id    = 42;
        $data->name  = 'amsify';
        $data->email = 'amsify@site.com';
        $data->price = 4.2;
        return $data;
    }
}