# PHP TypeStruct
PHP package for validating data against the structure defined.

### Installation
```
$ composer require amsify42/php-typestruct
```

## Table of Contents
1. [Introduction](#1-introduction)
2. [Validation](#2-validation)
3. [Data](#3-data)
4. [Class Validation](#4-class-validation)
5. [Rules](#5-rules)
6. [Custom Rules](#6-custom-rules)


### 1. Introduction
The purpose of this php package is to make validation easy and structure defined for validation shuold be readable. The data passed can be validated against the structure defined.

### 2. Validation
Let's say we have data in array format.
```php
$data = [
    'id' => 42,
    'name' => 'amsify',
    'price' => 4.2
];
```
and we want this to be strictly validated. Now we can define the structure against which this data to be validated.
```php
namespace App\TypeStruct;

export typestruct Simple {
    id: int,
    name: string,
    price: float
}
```
Notice that the structure we defined does not completely look like a PHP syntax but it will be parsed and working as a structure against data.
```php
$data = [
    'id' => 42,
    'name' => 'amsify',
    'price' => 4.2
];
$typeStruct = new Amsify42\TypeStruct\TypeStruct();
$typeStruct->setClass(App\TypeStruct\Simple::class);
$result = $typeStruct->validate($data);
```
Note we are creating new instance of `Amsify42\TypeStruct\TypeStruct`, passing the full class name of typestruct `App\TypeStruct\Simple` and pasing data to **validate()** method.
<br/><br/>
The validate method will return with the info whether the data passed is validated against the structure and it will return.
```txt
array(2) {
  ["is_validated"]=>
  bool(true)
  ["messages"]=>
  array(0) {
  }
}
```
The `is_validated` will have `true` or `false` based on whether data is validated or not and `messages` will have error messages in hierarchy based on elements which are not validated.

### 3. Data
The data you can pass for validation are
```txt
Array
Object(stdClass)
Json
XML
```
As we have already seen the example, lets see the examples for the rest
#### Object(stdClass)
```php
$data        = new \stdClass();
$data->id    = 42;
$data->name  = 'amsify';
$data->price = 4.2;    

$typeStruct = new Amsify42\TypeStruct\TypeStruct();
$typeStruct->isDataObject(true)->setClass(App\TypeStruct\Simple::class);
$result = $typeStruct->validate($data);
```
Note we are passing `true` to method `isDataObject()` to tell **TypeStruct** that the data we are passing is of type **Object(stdClass)**. 

#### Json
```php
$jsonData = '{"id":42,"name":"amsify","price":4.2}';
$typeStruct = new TypeStruct();
$typeStruct->contentType('json')->setClass(App\TypeStruct\Simple::class);
$result = $typeStruct->validate($jsonData);
```
#### XML
```php
$xmlData = '<?xml version="1.0" encoding="UTF-8" ?> <root> <id>42</id> <name>amsify</name> <price>4.2</price> </root>';
$typeStruct = new TypeStruct();
$typeStruct->contentType('xml')->setClass(App\TypeStruct\Simple::class);
$result = $typeStruct->validate($xmlData);
```
**Note:** We are calling **contentType()** method to set its type for both `Json` and `XML`.

### 4. Class Validation
We can do the validation by creating class and extending it to the `Amsify42\TypeStruct\Validator`
```php
<?php

namespace Amsify42\Validators;

use Amsify42\TypeStruct\Validator;

class Sample extends Validator
{
    protected $tsClass = \Amsify42\TypeStruct\Simple::class;

    protected $data = [
                        'id'    => 42,
                        'name'  => 'amsify',
                        'price' => 4.2
                    ];                 
}
```
Since we already set `TypeStruct` class name and `data` in **protected** properties. We can create instance of this class directly and validate
```php
$sample = new \Amsify42\Validators\Sample();
$result = $sample->validate();
```
and we can also use these protected properties which extends `Amsify42\TypeStruct\Validator`
```php
/**
 * Instead of setting typestruct class name, we can also set direct path of that typestruct file
 */
protected $tsPath;
/**
 * This will decide whether validation will stop at first error itself or when completing all validation errors. Default is true
 */
protected $validateFull;
/**
 * You can json or xml, default is empty string
 */
protected $contentType;
/**
 * Tells the typestruct whether the data we setting/passing is of type Object(stdClass)
 */
protected $isDataObject;
```

### 5. Rules
These are the basic types we can use for elements.
```php
export typestruct Sample {
    id: int,
    name: string,
    price: float,
    is_active: boolean,
    items: array
}
```
We can also set the limits to the length of these types like this
```php
export typestruct Sample {
    id: int(5),
    name: string(20),
    price: float(5,2),
    is_active: boolean,
    items: [5]
}
```
These are the array types we can use
```php
items: int[] 
items: string[]
items: float[]
items: boolean[]
```
We can also use array of other **TypeStruct** file as a child elements
```php
export typestruct Category {
    id: int,
    name: string
}
```
Now we can use `Category` as type in the other typestruct file like this
```php
use Category;
export typestruct Product {
    id: int,
    name: string,
    price: float,
    active: boolean,
    category: Category
}
```
or as child elements type for array of type `Category`
```php
use Category;
export typestruct Product {
    id: int,
    name: string,
    price: float,
    active: boolean,
    categories: Category[]
}
```

### 6. Custom Rules
We can also write method to perform cutom validation like but this can only be achieved when we create class and extends `Amsify42\TypeStruct\Validator`
```php
namespace App\TypeStruct;

export typestruct Simple {
    id: int,
    name: string<checkName>,
    price: float
}
```
Now we can write method `checkName` in our validator class like this
```php
<?php

namespace Amsify42\Validators;

use Amsify42\TypeStruct\Validator;

class Sample extends Validator
{
    protected $tsClass = \Amsify42\TypeStruct\Simple::class;

    protected $data = [
                        'id'    => 42,
                        'name'  => 'amsify',
                        'price' => 4.2
                    ];

    public function checkName()
    {
        if($this->value() !== 'amsify')
        {
            return 'Name should be amsify';
        }
        return true;
    }                                 
}
```
We can use `$this->value()` to get the active value of the element which is applicable to the rule. To get the other element value, we already have `$this->data` accessible from these custom rule methods.