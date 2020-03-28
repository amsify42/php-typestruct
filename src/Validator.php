<?php

namespace Amsify42\TypeStruct;

use Amsify42\TypeStruct\TypeStruct;
use Amsify42\TypeStruct\Validation\Rules;

class Validator extends Rules
{
    /**
     * TypeStruct class full name
     * @var null
     */
    protected $tsClass = NULL;
    /**
     * TypeStruct class full path
     * @var null
     */
    protected $tsPath = NULL;
    /**
     * TypeStruct instance
     * @var null
     */
    private $typeStruct = NULL;

    public function tsClass()
    {
        return $this->tsClass;
    }

    public function tsPath()
    {
        return $this->tsPath;
    }

    public function validate()
    {
        if(!$this->typeStruct)
        {
            $this->typeStruct = new TypeStruct();
            $this->typeStruct->setValidator($this);
        }
        return $this->typeStruct->validate();
    }
}