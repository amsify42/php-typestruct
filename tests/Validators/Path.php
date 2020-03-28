<?php

namespace Amsify42\Tests\Validators;

use Amsify42\TypeStruct\Validator;

class Path extends Validator
{
	protected $tsPath = TESTS_PATH.DS.'TypeStruct'.DS.'Simple.php';

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