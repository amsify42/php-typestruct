<?php

namespace Amsify42\Tests;

use PHPUnit\Framework\TestCase;
use Amsify42\TypeStruct\TypeStruct;

final class CustomRuleTest extends TestCase
{
    public function testValidator()
    {
        $sample = new \Amsify42\Tests\Validators\Custom();
        $result = $sample->validate();
        $this->assertArrayHasKey('is_validated', $result);
        $this->assertFalse($result['is_validated']);
    }

}