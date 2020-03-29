<?php

namespace Amsify42\Tests;

use PHPUnit\Framework\TestCase;
use Amsify42\TypeStruct\TypeStruct;

final class XMLTest extends TestCase
{
    private $tsClass = \Amsify42\Tests\TypeStruct\Simple::class;
    private $xmlData = '<?xml version="1.0" encoding="UTF-8" ?> <root> <id>42</id> <name>amsify</name> <email>amsify@site.com</email> <price>4.2</price> </root>';

    public function testXML()
    {
        $typeStruct = new TypeStruct();
        $typeStruct->contentType('xml')->setClass($this->tsClass);
        $result = $typeStruct->validate($this->xmlData);

        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);
    }

    public function testValidator()
    {
        $xml = new \Amsify42\Tests\Validators\XML();
        $result = $xml->validate();
        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);
    }
}