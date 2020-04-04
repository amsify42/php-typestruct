<?php

namespace Amsify42\Tests;

use PHPUnit\Framework\TestCase;
use Amsify42\TypeStruct\TypeStruct;

final class AnyTest extends TestCase
{
    private $tsClass = \Amsify42\Tests\TypeStruct\Any::class;
    private $arrData = [
                        'id'    => ['any','value'],
                        'name'  => 'amsify'
                    ];

    public function testArray()
    {
        $typeStruct = new TypeStruct();
        $typeStruct->setClass($this->tsClass);
        $result = $typeStruct->validate($this->arrData);
        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);
    }
}