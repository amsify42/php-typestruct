<?php

namespace Amsify42\Tests;

use PHPUnit\Framework\TestCase;
use Amsify42\TypeStruct\TypeStruct;

final class JsonTest extends TestCase
{
    private $tsClass = \Amsify42\Tests\TypeStruct\Simple::class;
    private $jsonData = '{"id":42,"name":"amsify","email":"amsify@site.com","price":4.2}';

    public function testJson()
    {
        $typeStruct = new TypeStruct();
        $typeStruct->contentType('json')->setClass($this->tsClass);
        $result = $typeStruct->validate($this->jsonData);

        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);
    }

    public function testValidator()
    {
        $json = new \Amsify42\Tests\Validators\Json();
        $result = $json->validate();
        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);
    }
}