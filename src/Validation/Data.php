<?php

namespace Amsify42\TypeStruct\Validation;

class Data
{
	public static function isInt($val)
	{
		return is_int($val);
	}

	public static function isStr($val)
	{
		return is_string($val);
	}

	public static function isFloat($val)
	{
		if(self::isInt($val))
		{
			$val = (float)$val;
		}
		return is_float($val);
	}

	public static function isBool($val)
	{
		return is_bool($val);
	}

	public static function isTinyInt($val)
	{
		return ($val == '0' || $val == '1')? true: false;
	}

	public static function fromJson($jsonStr)
	{
		return json_decode($jsonStr);
	}

	public static function isValidEmail($val)
	{
		return filter_var($val, FILTER_VALIDATE_EMAIL);
	}

	public static function isValidURL($val)
	{
		return filter_var($val, FILTER_VALIDATE_URL);
	}

	public static function isValidDate($val)
	{
		return strtotime($val);
	}

	public static function fromXML($xmlStr)
	{
		return self::fromArray(simplexml_load_string($xmlStr), true);
	}

	public static function fromArray($source, $numCheck=false)
	{
		if($numCheck)
		{
			return json_decode(json_encode($source, JSON_NUMERIC_CHECK));	
		}
		return json_decode(json_encode($source));
	}
}