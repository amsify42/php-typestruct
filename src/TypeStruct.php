<?php

namespace Amsify42\TypeStruct;

use Amsify42\TypeStruct\Validator;
use Amsify42\TypeStruct\Validation\Data;
use Amsify42\TypeStruct\Validation\Rules;
use stdClass;

class TypeStruct
{
	/**
	 * Path for generating typestruct file json
	 * @var string
	 */
	private static $generatedDir = __DIR__.DS.'generated';
	/**
	 * Path of the typestruct file
	 * @var null
	 */
	private $path = NULL;
	/**
	 * Updated timestamp of ts file
	 * @var null
	 */
	private $updated = NULL;
	/**
	 * Content of the typestruct file after filtering comments
	 * @var string
	 */
	private $content = '';
	/**
	 * Info of the typestruct file
	 * @var array
	 */
	private $info = [];
	/**
	 * If set to true will collect all error messages instead of getting the first one and exiting
	 * @var boolean
	 */
	private $validateFull = true;
	/**
	 * Content Type of data
	 * @var string
	 */
	private $contentType = '';
	/**
	 * $result
	 * @var array
	 */
	private $result = ['is_validated' => true, 'messages' => []];
	/**
	 * Reserved types
	 * @var array
	 */
	private $reservedTypes = ['string', 'int', 'float', 'boolean', 'any'];
	/**
	 * Reserved arrau types
	 * @var array
	 */
	private $arrayTypes = ['array', '[]', 'string', 'int', 'float', 'boolean'];
	/**
	 * structure
	 * @var array
	 */
	private $structure = [];
	/**
	 * Decides whether data is of type object
	 * @var boolean
	 */
	private $isDataObject = false;
	/**
	 * Generated Json file path 
	 * @var null
	 */
	private $jsonPath = NULL;
	/**
	 * token for separating the element level path
	 * @var string
	 */
	private $token = '.';
	/**
	 * Validator
	 * @var \Amsify42\TypeStruct\Validator
	 */
	private static $validator = NULL;
	/**
	 * Reserve type key
	 * @var string
	 */
	const TYPE_KEY = '__ty';
	/**
	 * Reserve Optional key
	 * @var string
	 */
	const OPT_KEY = '__opt';

	/**
	 * Set typestruct class path while instantiating
	 * @param string $path
	 */
	function __construct($path='')
	{
		if(self::$validator instanceof Validator)
		{
			self::$validator = NULL;
		}
		if($path)
		{
			$this->setPath($path);
		}	
	}

	/**
	 * Set typestruct generated json file path
	 * @param string
	 */
	public static function setGeneratedDir($path)
	{
		self::$generatedDir = $path;
	}

	/**
	 * Set Validate Full
	 * @param  boolean $set
	 * @return TypeStruct
	 */
	public function validateFull($set=true)
	{
		$this->validateFull = $set;
		return $this;
	}

	/**
	 * Set to inform whether data is already of type object
	 * @param boolean
	 * @return TypeStruct
	 */
	public function isDataObject($set=false)
	{
		$this->isDataObject = $set;
		return $this;
	}

	/**
	 * Set the data content type
	 * @return string
	 */
	public function contentType($type='')
	{
		$this->contentType = $type;
		return $this;
	}

	/**
	 * Set validator instance
	 * @param \Amsify42\TypeStruct\Validator $validator
	 * @return TypeStruct
	 */
	public function setValidator(Validator $validator)
	{
		self::$validator = $validator;
		if(self::$validator->tsClass())
		{
			$this->setClass();
		} else if(self::$validator->tsPath())
		{
			$this->setPath();
		}
		return $this;
	}

	/**
	 * Set typestruct class path
	 * @param string
	 * @return TypeStruct
	 */
	public function setPath($path=NULL)
	{
		$this->path = ($path)? $path: ((self::$validator && self::$validator instanceof Validator)? self::$validator->tsPath(): $path);
		$this->process();
		return $this;
	}

	/**
	 * Set class and it will find the path to file
	 * @param string
	 * @return TypeStruct
	 */
	public function setClass($class=NULL)
	{
		$class = ($class)? $class: ((self::$validator && self::$validator instanceof Validator)? self::$validator->tsClass(): $class);
		$this->path = $this->getFilePath($class);
		$this->process();
		return $this;
	}

	/**
	 * Processing of extracting content and parsing
	 */
	private function process()
	{
		$this->checkTSFile();
		/**
		 * Extract typestruct syntax and save to json only if typestruct is new or modified
		 */
		if($this->isTSEdited())
		{
			$this->extractInfo();
			$this->structure = $this->structToObject($this->content);
			$this->generateJson();
		}
	}

	/**
	 * Check if typestruct file exist at the given path
	 */
	private function checkTSFile()
	{
		if(!is_file($this->path))
		{
			throw new \RuntimeException('TypeStruct['.$this->path.'] not found');
		}
		$this->jsonPath = self::$generatedDir.DS.md5($this->path).'.json';
	}

	/**
	 * Check if typestruct file is edited
	 * @return boolean
	 */
	private function isTSEdited()
	{
		$jsonFile = json_decode($this->getJsonFile(), true);
		$updated  = filemtime($this->path);
		if(!$jsonFile || ($jsonFile['updt'] != $updated) || ($jsonFile['valFull'] != $this->validateFull))
		{
			$this->updated = $updated;
			return true;
		}
		else
		{
			$this->validateFull = $jsonFile['valFull'];
			$this->structure 	= $jsonFile['struct'];
			return false;
		}
	}

	/**
	 * Extract typestruct syntax and info from it.
	 */
	private function extractInfo()
	{
		$this->content = file_get_contents($this->path);
		/**
		 * Removing commented lines
		 */
		$this->content = preg_replace('/\/\*[\s\S]+?\*\//', '', $this->content);
		$this->content = preg_replace('![ \t]*//.*[ \t]*[\r\n]!', '', $this->content);
		/**
		 * Find class name of typestruct
		 */
		preg_match('/export typestruct(.*?){/ims', $this->content, $matches);
		if(isset($matches[1]))
		{
			$this->info['name'] = trim($matches[1]);
		}
		/**
		 * Find full class name of typestruct
		 */
		$fullName = '';
		preg_match('/namespace(.*?);/ims', $this->content, $matches);
		if(isset($matches[1]))
		{
			$this->info['namespace'] = trim($matches[1]);
			$fullName = $this->info['namespace']."\\".trim($this->info['name']);
		}
		else
		{
			$fullName = $this->info['name'];
		}
		$this->info['full_name'] = $fullName;
		/**
		 * Find used namespaces of typestruct
		 */
		$classes = [];
		preg_match_all('/use(.*?);/ims', $this->content, $matches);
		if(isset($matches[1]))
		{
			$classes = array_map('trim', $matches[1]);
		}
		$this->info['used_typestructs'] = $classes;
	}

	/**
	 * Convert typestruct structure string to info array
	 * @param  string $structString
	 * @return array
	 */
	public function structToObject($structString)
	{
		$structure = [];
		$pairs 	   = $this->extractDictionary($structString);
		foreach($pairs as $pair)
		{
			$subPairs = $this->extractDictionary($pair);
			if(sizeof($subPairs) > 0)
			{
				foreach($subPairs as $subPair)
				{
					$pair = str_replace($subPair, '', $pair);
				}
			}
			$elements = explode(',', $pair);
			$subPairIndex = 0;
			foreach($elements as $element)
			{
				$elementArray = explode(':', trim($element));
				if(sizeof($elementArray)> 1)
				{
					$segment = trim($elementArray[1]);
					$isDict  = ($segment == '{}' || $segment == '?{}')? true: false;
					$isOpt 	 = ($segment == '?{}')? true: false;
					if($isDict)
					{
						$structure[trim($elementArray[0])] = isset($subPairs[$subPairIndex])? $this->structToObject('{'.$subPairs[$subPairIndex].'}'): 'NULL';
						if($isOpt)
						{
							$structure[trim($elementArray[0])][self::OPT_KEY] = true;
						}
						$subPairIndex++;
					}
					else
					{
						$result = $this->isValidType($segment);
						if($result['is_validated'])
						{
							$structure[trim($elementArray[0])] = $result[self::TYPE_KEY];
						}
						else
						{
							throw new \RuntimeException('Invalid Data Type:'.$segment);
						}
					}
				}
			}
		}
		return $structure;
	}

	/**
	 * Extract Dictionary from typestruct file
	 * @param  string $string
	 * @return array
	 */
	public function extractDictionary($string)
	{
		preg_match_all('/{((?:[^{}]*|(?R))*)}/x', $string, $matches);
		return isset($matches[1])? $matches[1]: [];
	}

	/**
	 * Get TypeStruct class instance
	 * @param  array|object $data
	 * @return array
	 */
	public function validate($data=null)
	{		
		/**
		 * Get data from class if validator is of type Amsify42\TypeStruct\Validator
		 */
		if(self::$validator && self::$validator instanceof Validator)
		{
			$this->validateFull = self::$validator->validateFull();
			
			$data = self::$validator->data();
		}
		/**
		 * Instantiating Amsify42\TypeStruct\Rules class if not of type Amsify42\TypeStruct\Validator
		 */
		else if(!self::$validator)
		{
			self::$validator = new Rules();
		}

		/**
		 * Check if contentType is set in Validator class or in TypeStruct instance
		 */
		$contentType = (self::$validator->contentType())? self::$validator->contentType(): $this->contentType;
		if($contentType)
		{
			if($contentType == 'json')
			{
				$data = Data::fromJson($data);
			}
			else if($contentType == 'xml')
			{
				$data = Data::fromXML($data);
			}
		}
		else
		{
			/**
			 * Convert it to pure stdClass/Object if it is array
			 */
			if((self::$validator instanceof Validator && !self::$validator->isDataObject()) || !$this->isDataObject)
			{
				$data = Data::fromArray($data);
			}
		}

		if(self::$validator instanceof Rules)
		{
			/**
			 * Assigning data to Rules array simple
			 * And making it accessible while performing validation rules
			 */
			self::$validator->setArraySimple($data);
		}
		/**
		 * Start iterating data for validation
		 */
		$response = $this->iterateDictionary($data);
		/**
		 * If validateFull is set to true will get all validation errors
		 */
		if($this->validateFull && sizeof($this->result['messages'])> 0)
		{
			$this->result['is_validated'] = false;
			return $this->result;
		}
		else
		{
			return $response;
		}
	}

	/**
	 * Iterate dictionary info collected
	 * @param  object      $data
	 * @param  string|null $path
	 * @param  array|null  $dictionary
	 * @return array
	 */
	private function iterateDictionary($data=null, $path=null, $dictionary=null)
	{
		$result = ['is_validated' => true, 'messages' => []];

		$structure = ($dictionary)? $dictionary: $this->structure;
		if($structure)
		{
			foreach($structure as $name => $info)
			{
				/**
				 * If element not found
				 */
				if(!isset($data->{$name}))
				{
					/**
					 * If element is not optional
					 */
					if(!isset($info[self::OPT_KEY]))
					{
						if(is_array($info) || ($info != 'any' && strtolower($info) != 'null'))
						{
							$result['is_validated'] = false;
							if($this->validateFull)
							{
								$this->setPathKeyMessage($path, $this->getMessage('', '', 'missing'), $this->result['messages'], $name);
							}
							else
							{
								$this->setPathKeyMessage($path, $this->getMessage('', '', 'missing'), $result['messages'], $name);
								break;
							}
						}
					}
				}
				else
				{
					/**
					 * If element is child
					 */
					if(!isset($info[self::TYPE_KEY]))
					{
						$childPath = ($path)? $path.$this->token.$name: $name;
						$validated = $this->iterateDictionary($data->{$name}, $childPath, $info);
						if(!$validated['is_validated'])
						{
							$result['is_validated'] = false;
							$result['messages'] 	= $validated['messages'];
							if(!$this->validateFull)
							{
								break;
							}
						}
					}
					/**
					 * If element is of type array
					 */
					else if($info[self::TYPE_KEY] == 'array')
					{
						$arrResult = $this->checkArrayType($name, $data->{$name}, $info);
						if(!$arrResult['is_validated'])
						{
							$result['is_validated'] = false;
							if($this->validateFull)
							{
								$this->setPathKeyMessage($path, $arrResult['message'], $this->result['messages'], $name, $arrResult['child_err']);
							}
							else
							{
								$this->setPathKeyMessage($path, $arrResult['message'], $result['messages'], $name, $arrResult['child_err']);
								break;
							}
						}
					}
					else
					{
						/**
						 * If element exist and forwarded for validation
						 */
						$typeResult = $this->checkType($name, $data->{$name}, $info);
						if(!$typeResult['is_validated'])
						{
							$result['is_validated'] = false;
							if($this->validateFull)
							{
								$this->setPathKeyMessage($path, $typeResult['message'], $this->result['messages'], $name);
							}
							else
							{
								$this->setPathKeyMessage($path, $typeResult['message'], $result['messages'], $name);
								break;
							}
						}
					}
				}			
			}
		}
		return $result;
	}

	/**
	 * Check if type is valid
	 * @param  string  $type
	 * @return array
	 */
	private function isValidType($type)
	{
		$type 	= trim($type);
		$matches= [];
		preg_match('/\<(.*?)\>/ims', $type, $matches);
		$rules = [];
		if(sizeof($matches)> 0)
		{
			if(isset($matches[1]))
			{
				$str = trim($matches[1]);
				if($str)
				{
					$rules = explode('.', $matches[1]);
				}
			}
			$type = str_replace($matches[0], '', $type);
		}
		$isQues = ($type[0] == '?')? true: false;
		$result = ['is_validated' => false, self::TYPE_KEY => []];
		if($isQues)
		{
			$type = str_replace('?', '', $type);
		}
		$vType 	= '';
		if($this->isTypeArray($type))
		{
			$vType     = 'array'; 
			$arrayType = trim($this->findArrayType($type));
			if(!$arrayType || in_array($arrayType, $this->arrayTypes))
			{
				$result['is_validated'] = true;
				$result[self::TYPE_KEY] = [self::TYPE_KEY => 'array'];
				if($arrayType && $arrayType != '[]' && $arrayType != 'array')
				{
					$result[self::TYPE_KEY]['of'] = $arrayType;
				}
			}
			else
			{
				$info = $this->checkExternalTypestruct($arrayType);
				if($info['is_validated'])
				{
					$result['is_validated'] = true;
					$result[self::TYPE_KEY] = [self::TYPE_KEY => 'array', 'of' => $info[self::TYPE_KEY]];
				}
			}
		}
		else
		{
			$typeArr = explode('(', $type);
			$gType 	 = isset($typeArr[0])? trim($typeArr[0]): '';
			if(in_array($gType, $this->reservedTypes))
			{
				$result['is_validated'] = true;
				$result[self::TYPE_KEY] = [self::TYPE_KEY => $gType];
			}
			else
			{
				$info = $this->checkExternalTypestruct($gType);
				if($info['is_validated'])
				{
					$result['is_validated'] = true;
					$result[self::TYPE_KEY] = [self::TYPE_KEY => $info[self::TYPE_KEY]];
				}
			}
		}
		$lenInfo = $this->findLengthInfo($type, $vType);
		if(!empty($lenInfo))
		{
			$result[self::TYPE_KEY]['length'] = (int)$lenInfo[0];
			if(isset($lenInfo[1]))
			{
				$result[self::TYPE_KEY]['decimal'] = (int)$lenInfo[1];
			}
		}
		if($isQues)
		{
			$result[self::TYPE_KEY][self::OPT_KEY] = true;
		}
		if(!empty($rules))
		{
			$result[self::TYPE_KEY]['rules'] = $rules;
		}
		return $result;
	}

	/**
	 * Check if type is array
	 * @param  string  $type
	 * @return boolean
	 */
	private function isTypeArray($type)
	{
		return (trim($type) == 'array' || preg_match("/\[(\d+(,\d+)*)?\]$/i", $type));
	}

	/**
	 * Find Array Type of key
	 * @param  string $type
	 * @return string
	 */
	private function findArrayType($content)
	{
		$infoArr = explode('[', $content);
		return trim($infoArr[0]);
	}

	/**
	 * Check Resource Type full class name
	 * @param  string $type
	 * @return array
	 */
	private function checkExternalTypestruct($type)
	{
		$resource = $type;
		$info 	  = ['is_validated' => true, self::TYPE_KEY => $type];
		if(sizeof($this->info['used_typestructs'])> 0)
		{
			foreach($this->info['used_typestructs'] as $class)
			{
				if(strpos($class, $type) !== false)
				{
					$resource = $class;
					break;
				}
			}
		}
		$this->checkTSFile($this->getFilePath($resource));
		$info[self::TYPE_KEY] = $resource;
		return $info;
	}

	/**
	 * Find Length Info of type
	 * @param  string $content
	 * @param  string $type
	 * @return array
	 */
	private function findLengthInfo($content, $type='')
	{
		$matches = [];
		if($type == 'array')
		{
			preg_match('/\[(.*?)\]/ims', $content, $matches);
		}
		else
		{
			preg_match('/\((.*?)\)/ims', $content, $matches);
		}
		if(isset($matches[1]))
		{
			return explode('.', $matches[1]);
		}
		return [];
	}

	/**
	 * Generate typestruct info in Json file and save
	 */
	private function generateJson()
	{
		if(!file_exists(self::$generatedDir))
		{
		    mkdir(self::$generatedDir, 0777, true);
		}
		$data 		   = new stdClass();
		$data->updt    = $this->updated;
		$data->valFull = $this->validateFull;
		$data->struct  = $this->structure;
		file_put_contents($this->jsonPath, json_encode($data));
	}

	/**
	 * Get Json file if existing
	 * @return jsonString|NULL
	 */
	private function getJsonFile()
	{
		return (is_file($this->jsonPath))? file_get_contents($this->jsonPath): NULL;
	}

	/**
	 * Path of the typestruct/data element 
	 * @param  string $path
	 * @param  mixed $message
	 * @param  mixed $messages
	 * @param  string $name
	 * @param  boolean $childErr
	 */
	private function setPathKeyMessage($path, $message, &$messages, $name, $childErr=false)
	{
		$path = rtrim($path, $this->token);
		if($path)
		{
			$pArr = explode($this->token, $path);
			if(sizeof($pArr)> 0)
			{
				for($i=&$messages; $key=array_shift($pArr),$key!==NULL; $i=&$i[$key])
				{
					if(!isset($i[$key]))
					{
						$i[$key] = [];
					}
			    }
		    	$key = ($childErr)? $name.'[]': $name;
		    	$i[$key] = $message;
			}
		}
		else
		{
	    	$key = ($childErr)? $name.'[]': $name;
	    	$messages[$key] = $message;
		}
	}

	/**
	 * Check array type
	 * @param  string $name
	 * @param  mixed $value
	 * @param  array  $info
	 * @return array
	 */
	private function checkArrayType($name, $value, $info)
	{
		$result  = ['is_validated' => true, 'message' => '', 'child_err' => false];
		$isArray = is_array($value);
		if(!$isArray)
		{
			$result['is_validated'] = false;
			$result['message'] 	= $this->getMessage('array');
		}
		else
		{
			if(!isset($info[self::OPT_KEY]) && empty($value))
			{
				$result['is_validated'] = false;
				$result['message'] =  $this->getMessage('', '', 'missing');
			}
			else
			{
				if(isset($info['length']) && $info['length'])
				{
					if(($isArray && sizeof($value) > $info['length']))
					{
						$result['child_err'] = true;
						$result['is_validated'] = false;
						$result['message'] = $this->getMessage('', $info['length'], 'length');
						return $result;
					}
				}
				if(isset($info['of']) && $isArray)
				{
					if($info['of'] == 'string')
					{
						foreach($value as $vk => $el)
						{
							if(!Data::isStr($el))
							{
								$result['child_err'] = true;
								$result['is_validated'] = false;
								$result['message'] 	= $this->getMessage('array', 'string');
								break;
							}
						}
					}
					else if($info['of'] == 'int')
					{
						foreach($value as $vk => $el)
						{
							if(!Data::isInt($el))
							{
								$result['child_err'] = true;
								$result['is_validated'] = false;
								$result['message'] 	= $this->getMessage('array', 'integer');
								break;
							}
						}
					}
					else if($info['of'] == 'float')
					{
						foreach($value as $vk => $el)
						{
							if(!Data::isFloat($el))
							{
								$result['child_err'] = true;
								$result['is_validated'] = false;
								$result['message'] 	= $this->getMessage('array', 'float');
								break;
							}
						}
					}
					else if($info['of'] == 'boolean')
					{
						foreach($value as $vk => $el)
						{
							if(!Data::isBool($el))
							{
								$result['child_err'] = true;
								$result['is_validated'] = false;
								$result['message'] 	= $this->getMessage('array', 'boolean');
								break;
							}
						}
					}
					else
					{
						$typeStruct = $this->newByFileName($info['of']);
						foreach($value as $vk => $val)
						{
							$childResult = $typeStruct->validate($val);
							if(!$childResult['is_validated'])
							{
								$result['child_err'] = true;
								$result['is_validated'] = false;
								$result['message'] = $childResult['messages'];
								break;
							}
						}
					}
				}
			}	
		}
		if($result['is_validated'])
		{
			$result = $this->checkRules($name, $info, $result, $value);
		}
		return $result;
	}

	/**
	 * Check value type
	 * @param  string $name
	 * @param  mixed $value
	 * @param  array  $info
	 * @return array
	 */
	private function checkType($name, $value, $info)
	{
		$result = ['is_validated' => true, 'message' => ''];
		if(Data::isStr($value))
		{
			$value = trim($value);
		}
		/**
		 * Check if the value is empty
		 */
		if($value !== 0 && empty($value))
		{
			/**
			 * Skip if it is optional
			 */
			if(!isset($info[self::OPT_KEY]) || !$info[self::OPT_KEY])
			{
				$result['is_validated'] = false;
				$result['message'] = $this->getMessage('', '', 'missing');
			}
		}
		/**
		 * Else process of type checking
		 */
		else
		{
			switch($info[self::TYPE_KEY])
			{
				case 'string':
					if(!Data::isStr($value))
					{
						$result['is_validated'] = false;
						$result['message'] = $this->getMessage('string');
					}
					break;

				case 'int':
					if(!Data::isInt($value))
					{
						$result['is_validated'] = false;
						$result['message'] = $this->getMessage('integer');
					}
					break;

				case 'float':
					if(!Data::isFloat($value))
					{
						$result['is_validated'] = false;
						$result['message'] = $this->getMessage('float');
					}
					break;
				case 'boolean':
					if(!Data::isBool($value))
					{
						$result['is_validated'] = false;
						$result['message'] = $this->getMessage('boolean');
					}
					break;

				case 'any':
					break;	

				default:
					/**
					 * If type is external typestruct
					 */
					$childResult = $this->newByFileName($info[self::TYPE_KEY])->validate($value);
					if(!$childResult['is_validated'])
					{
						$result['is_validated'] = false;
						$result['message'] = $childResult['messages'];
					}
					
					break;
			}

			if($result['is_validated'])
			{
				if(isset($info['length']) && $info['length'])
				{
					$result = $this->checkLength($name, $value, $info[self::TYPE_KEY], $info['length']);
				}
				$result = $this->checkRules($name, $info, $result, $value);
			}
		}

		return $result;
	}

	/**
	 * Created new instance of TypeStruct
	 * @param  string $fileName
	 * @return App\Core\Data\TypeStruct
	 */
	private function newByFileName($fileName)
	{
		return new TypeStruct($this->getFilePath($fileName));
	}

	/**
	 * Check validation rule methods in validator class
	 * @param  string $name
	 * @param  array $info
	 * @param  array $result
	 * @param  mixed $value
	 * @return array
	 */
	private function checkRules($name, $info, $result, $value)
	{
		if(isset($info['rules']) && $result['is_validated'])
		{
			if(sizeof($info['rules'])> 0)
			{
				foreach($info['rules'] as $ck => $method)
				{
					if(method_exists(self::$validator, $method) && is_callable([self::$validator, $method]))
					{
						self::$validator->setValue($value);
						$cResult = call_user_func_array([self::$validator, $method], []);
						if($cResult !== true)
						{
							$result['is_validated'] = false;
							$result['message'] 		= Data::isStr($cResult)? $cResult: 'Invalid value';
							break;
						}
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Check length of the value
	 * @param  string $name
	 * @param  mixed $value
	 * @param  string $type
	 * @param  int    $length
	 * @return array
	 */
	public static function checkLength($name, $value, $type, $length)
	{
		$result = ['is_validated' => true, 'message' => ''];
		$val 	= (string)$value;
		if($type == 'float')
		{
			$val = explode('.', $val)[0];
		}
		if(strlen($val) > $length)
		{
			$result['is_validated'] = false;
			$result['message'] = $this->getMessage('', $length, 'length');
		}
		return $result;
	}

	/**
	 * Get file path
	 * @param  string $className
	 * @return string
	 */
	private function getFilePath($className)
	{
		$path = '../../vendor/autoload.php';
		/**
		 * Check if the script is running from app scope
		 */
		if(!is_file($path))
		{
			/**
			 * Else assign dev autoload scope script path
			 */
			$path = 'vendor/autoload.php';
		}
		$loader 	= require $path;
		$loggerPath = $loader->findFile($className);
		if($loggerPath)
		{
			return realpath($loggerPath);
		}
		else
		{
			throw new \RuntimeException("Cannot find file for class '$className'");
		}
	}

	/**
	 * Get message
	 * @param  string $type
	 * @param  string $of
	 * @param  string $mType
	 * @return string
	 */
	private function getMessage($type='', $of='', $mType='type')
	{
		if($mType == 'type')
		{
			if($type)
			{
				if($of)
				{
					return 'must be an array of '.$of;
				}
				else
				{
					return 'must be of type '.$type;
				}
			}	
		}
		else if($mType == 'missing')
		{
			return 'is required';
		}
		else if($mType == 'length')
		{
			return 'max length allowed is '.$of;
		}
		return '';
	}
}