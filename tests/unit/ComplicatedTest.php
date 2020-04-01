<?php

namespace Amsify42\Tests;

use PHPUnit\Framework\TestCase;
use Amsify42\TypeStruct\TypeStruct;

final class ComplicatedTest extends TestCase
{
    private $tsClass = \Amsify42\Tests\TypeStruct\Complicated::class;
    private $arrData = [
                        'name' => 'amsify',
                        'user' => [
                            'id' => 1,
                            'name' => 'some',
                            'email' => 'some@site.com'
                        ],
                        'address' => [
                            'door' => '12-3-534',
                            'zip' => 600035
                        ],
                        'url' => 'https://www.site.com/page.html',
                        'items' => [1,2,3,4,5,6,7],
                        'someEl' => [
                            'key1' => 'val1',
                            'key2' => 2,
                            'key12' => [1,2,12],
                            'records' => [
                                [
                                    'id' => 1,
                                    'name' => 'r1'
                                ],
                                [
                                    'id' => 2,
                                    'name' => 'r2'
                                ]
                            ],
                            'someChild' => [
                                'key3' => true,
                                'key4' => 4.01,
                                'someAgainChild' => [
                                    'key5' => 'val5',
                                    'key6' => 6.4,
                                    'key56' => [true,false,true]
                                ]
                            ]
                        ]
                    ];

    public function testComlicated()
    {
        $typeStruct = new TypeStruct();
        $typeStruct->setClass($this->tsClass);
        $result = $typeStruct->validate($this->arrData);
        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);
    }
}