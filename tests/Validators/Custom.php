<?php

namespace Amsify42\Tests\Validators;

use Amsify42\TypeStruct\Validator;

class Custom extends Validator
{
	protected $tsClass = \Amsify42\Tests\TypeStruct\Custom::class;

	protected $data = [
                        'id'    => 42,
                        'type'  => 'six',
                        'details' => [
                        	'number' => 2,
                        	'price' => 9.01
                        ]
                    ];

    private $types = ['one', 'two', 'three', 'four', 'five', 'six'];                

   public function checkEnumTypes()
   {
   		if(!in_array($this->value(), $this->types))
   		{
   			return "Type should be from ['".implode("','", $this->types)."']";
   		}
   		return true;
   }

   public function checkIdPrice()
   {
   		if($this->value() == 42 && $this->path('details.price') < 10)
   		{
   			return 'UserId: 42 should not have price less than 10';
   		}
   		return true;
   }               
}