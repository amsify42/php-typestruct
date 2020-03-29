<?php

namespace Amsify42\TypeStruct\Validation;

class Data
{
	public static function fromJson($jsonStr)
	{
		return json_decode($jsonStr);
	}

	public static function fromXML($xmlStr)
	{
		return self::fromArray(simplexml_load_string($xmlStr));
	}

	public static function fromArray($source)
	{
		return json_decode(json_encode($source, JSON_NUMERIC_CHECK));
	}
}