<?php

namespace Amsify42\Tests\Validators;

use Amsify42\TypeStruct\Validator;

class Rules extends Validator
{
	protected $tsClass = \Amsify42\Tests\TypeStruct\Rules::class;

	protected $data = [
                        'email' => 'amsify@site.com',
                        'url'   => 'https://www.site.com',
                        'date'  => '2nd June 2020'
                    ];

   public function checkHost()
   {
   		$domain = parse_url($this->value(), PHP_URL_HOST);
   		if(strtolower($domain) == 'www.google.com')
   		{
   			return 'Google is not allowed';
   		}
   		return true;
   }                 
}