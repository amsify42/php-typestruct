<?php

/**
 * Get new \Amsify42\TypeStruct\TypeStruct instance
 * @param  string  $source
 * @param  string  $type
 * @param  boolean $isObject
 * @return \Amsify42\TypeStruct\TypeStruct
 */
function get_typestruct($source, $type='path', $isObject=false)
{
    $typeStruct = new \Amsify42\TypeStruct\TypeStruct();
    if($type == 'class')
    {
        $typeStruct->setClass($source);
    }
    else
    {
        $typeStruct->setPath($source);
    }
    return $typeStruct->isDataObject($isObject);
}