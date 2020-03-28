<?php

namespace Amsify42\Tests\Validators;

use Amsify42\TypeStruct\Validator;

class Complicated extends Validator
{
	protected $tsClass = \Amsify42\Tests\TypeStruct\Complex::class;

	protected $data = [
                        'uid'     => 42,
                        'title'   => 'amsify42',
                        'simples' => [
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

   public function checkTitle()
   {
   		if($this->value() !== 'amsify42')
   		{
   			return 'Name should be amsify';
   		}
   		return true;
   }                 
}