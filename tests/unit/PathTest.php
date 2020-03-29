<?php

namespace Amsify42\Tests;

use PHPUnit\Framework\TestCase;
use Amsify42\TypeStruct\TypeStruct;

final class PathTest extends TestCase
{
    private $tsPath  = TESTS_PATH.DS.'TypeStruct'.DS.'Simple.php';
    private $arrData = [
                        'id'    => 42,
                        'name'  => 'amsify',
                        'email' => 'amsify@site.com',
                        'price' => 4.2
                    ];

    public function testArray()
    {
        $typeStruct = new TypeStruct();
        $typeStruct->setPath($this->tsPath);
        $result = $typeStruct->validate($this->arrData);

        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);
    }

    public function testObject()
    {
        $typeStruct = new TypeStruct();
        $typeStruct->isDataObject(true)->setPath($this->tsPath);
        $result = $typeStruct->validate($this->getStdClass());

        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);   
    }

    public function testValidator()
    {
        $sample = new \Amsify42\Tests\Validators\Path();
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