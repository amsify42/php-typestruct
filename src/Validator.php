<?php

namespace Amsify42\TypeStruct;

use Amsify42\TypeStruct\TypeStruct;
use Amsify42\TypeStruct\Validation\Rules;

class Validator extends Rules
{
    protected $typeStruct = NULL;

    protected $loadAsArray = false;

    protected $data = NULL;

    public function loadAsArray()
    {
        return $this->loadAsArray;
    }

    public function typeStruct()
    {
        return $this->typeStruct;
    }

    public function data()
    {
        return $this->data;
    }

    public function validate()
    {
        $typeStruct = new TypeStruct();
        $typeStruct->setValidator($this);
        return $typeStruct->validate();
    }
}