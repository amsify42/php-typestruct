<?php

namespace Amsify42\Tests;

use PHPUnit\Framework\TestCase;
use Amsify42\TypeStruct\TypeStruct;

final class OptionalTest extends TestCase
{
    private $tsClass = \Amsify42\Tests\TypeStruct\Optional::class;
    private $arrData = [
                        'name'  => 'amsify',
                    ];

    public function testWithoutOptional()
    {
        $typeStruct = new TypeStruct();
        $typeStruct->setClass($this->tsClass);
        $result = $typeStruct->validate($this->arrData);
        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);
    }

    public function testWithOptional()
    {
        $this->arrData['detail'] = [
            'id' => 42,
        ];
        $typeStruct = new TypeStruct();
        $typeStruct->setClass($this->tsClass);
        $result = $typeStruct->validate($this->arrData);
        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);
    }

    public function testWithOptional2()
    {
        $this->arrData['detail'] = [
            'id' => 42,
            'name' => 12
        ];
        $typeStruct = new TypeStruct();
        $typeStruct->setClass($this->tsClass);
        $result = $typeStruct->validate($this->arrData);
        $this->assertArrayHasKey('is_validated', $result);
        $this->assertFalse($result['is_validated']);
    }
}