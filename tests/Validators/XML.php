<?php

namespace Amsify42\Tests\Validators;

use Amsify42\TypeStruct\Validator;

class XML extends Validator
{
	protected $tsClass = \Amsify42\Tests\TypeStruct\Simple::class;

	protected $data = '<?xml version="1.0" encoding="UTF-8" ?> <root> <id>42</id> <name>amsify</name> <email>amsify@site.com</email> <price>4.2</price> </root>';

	protected $contentType = 'xml';

   	public function checkName()
   	{
   		if($this->value() !== 'amsify')
   		{
   			return 'Name should be amsify';
   		}
   		return true;
   	}                 
}