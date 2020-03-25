<?php

namespace Amsify42\Tests\Validators;

use Amsify42\TypeStruct\Validator;

class Sample extends Validator
{
	protected $typeStruct = \Amsify42\Tests\TypeStruct\Simple::class;

	protected $data = [
                        'id'    => 42,
                        'name'  => 'amsify',
                        'price' => 4.2
                    ];
}