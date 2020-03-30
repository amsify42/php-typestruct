<?php

namespace Amsify42\Tests;

use PHPUnit\Framework\TestCase;
use Amsify42\TypeStruct\TypeStruct;

final class ComplexTest extends TestCase
{
    private $tsClass = \Amsify42\Tests\TypeStruct\Complex::class;
    private $arrData = [
                        'uid'     => 42,
                        'title'   => 'amsify42',
                        'simples' => [
                            [
                                'id'    => 40,
                                'name'  => 'amsify',
                                'email' => 'amsify@site.com',
                                'price' => 4.0
                            ],
                            [
                                'id'    => 41,
                                'name'  => 'amsify1',
                                'email' => 'amsify1@site.com',
                                'price' => 4.1
                            ],
                            [
                                'id'    => 43,
                                'name'  => 'amsify3',
                                'email' => 'amsify3@site.com',
                                'price' => 4.3
                            ]
                        ]
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

    public function testValidator()
    {
        $complicated = new \Amsify42\Tests\Validators\Complicated();
        $result = $complicated->validate();
        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);
    }

    private function getStdClass()
    {
        $data          = new \stdClass();
        $data->uid     = 42;
        $data->title   = 'amsify';
        $data->email   = 'amsify@site.com';
        $data->simples = [];

        $item        = new \stdClass();
        $item->id    = 41;
        $item->name  = 'amsify1';
        $item->email = 'amsify1@site.com';
        $item->price = 4.1;
        $data->simples[] = $item;

        $item        = new \stdClass();
        $item->id    = 43;
        $item->name  = 'amsify3';
        $item->email = 'amsify3@site.com';
        $item->price = 4.3;
        $data->simples[] = $item;     

        return $data;
    }                
}