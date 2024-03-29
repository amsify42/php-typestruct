<?php

namespace Amsify42\TypeStruct\Validation;

use Amsify42\PHPVarsData\Data\ArraySimple;

class Rules
{
    /**
     * Validate full
     * @var boolean
     */
    private $validateFull = true;
    /**
     * Content Type of data
     * @var string
     */
    protected $contentType = '';
    /**
     * data that needs to be validated
     * @var null
     */
    protected $data = NULL;
    /**
     * Decides whether data is object or not
     * @var boolean
     */
    protected $isDataObject  = false;
    /**
     * Array Simple
     * @var null
     */
    private $arraySimple  = NULL;
    /**
     * It sets the active data element name applicable for rules
     * @var null
     */
    private $name = NULL;
    /**
     * It sets the active data element value applicable for rules
     * @var null
     */
    private $value = NULL;

    protected function name()
    {
        return $this->name;
    }

    protected function value()
    {
        return $this->value;
    }

    protected function path($path)
    {
        $this->setArraySimple();
        return $this->arraySimple->get($path);
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function setArraySimple($data=NULL)
    {
        if(!$this->arraySimple)
        {
            if($data || $this->data)
            {
                $this->arraySimple = new ArraySimple(json_decode(json_encode(($data)? $data: $this->data), true));
            }
            else
            {
                $this->arraySimple = new ArraySimple([]);   
            }
        }
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function validateFull()
    {
        return $this->validateFull;
    }

    public function contentType()
    {
        return $this->contentType;
    }

    public function isDataObject()
    {
        return $this->isDataObject;
    }

    public function data()
    {
        return $this->data;
    }

    /**
     * Default rules
     */
    public function nonEmpty()
    {
        $value = Data::isStr($this->value)? trim($this->value): $this->value;
        if(empty($value))
        {
            return 'Cannot be empty';
        }
        return true;
    }

	public function email()
    {
        if(Data::isValidEmail($this->value) === false)
        {
            return 'Invalid email';
        }
        return true;
    }

    public function url()
    {
        if(Data::isValidURL($this->value) === false)
        {
            return 'Invalid url';
        }
        return true;
    }

    public function date()
    {
        if(Data::isValidDate($this->value) === false)
        {
            return 'Invalid date';
        }
        return true;
    }
}