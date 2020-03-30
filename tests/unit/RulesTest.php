<?php

namespace Amsify42\Tests;

use PHPUnit\Framework\TestCase;
use Amsify42\TypeStruct\TypeStruct;

final class RulesTest extends TestCase
{
    private $tsClass = \Amsify42\Tests\TypeStruct\Rules::class;
    private $arrData = [
                        'email' => 'amsify@site.com',
                        'url'   => 'https://www.site.com',
                        'date'  => '2nd June 2020'
                    ];

    public function testRules()
    {
        $typeStruct = new TypeStruct();
        $typeStruct->setClass($this->tsClass);
        $result = $typeStruct->validate($this->arrData);
        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);
    }

    public function testValidator()
    {
        $sample = new \Amsify42\Tests\Validators\Rules();
        $result = $sample->validate();
        $this->assertArrayHasKey('is_validated', $result);
        $this->assertTrue($result['is_validated']);
    }
}