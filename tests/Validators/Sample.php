<?php

namespace Amsify42\Tests\Validators;

use Amsify42\TypeStruct\Validator;

class Sample extends Validator
{
	protected $tsClass = \Amsify42\Tests\TypeStruct\Simple::class;

	protected $data = [
                        'id'    => 42,
                        'name'  => 'amsify',
                        'email' => 'amsify@site.com',
                        'price' => 4.2
                    ];

   public function checkName()
   {
   		if($this->value() !== 'amsify')
   		{
   			return 'Name should be amsify';
   		}
   		return true;
   }                 
}