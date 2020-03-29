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
     * It sets the active data element value applicable for rules
     * @var null
     */
    private $value = NULL;

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

	public function email()
    {
        if(filter_var($this->value, FILTER_VALIDATE_EMAIL))
        {
            return true;
        }
        else
        {
            return 'Invalid email';
        }
    }
}