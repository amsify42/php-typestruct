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
The purpose of this php package is to make validation easy and the structure defined for validation should be readable. The data passed can be validated against the structure defined.

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
Notice that the structure we defined does not completely look like a PHP syntax but it will work as a structure against data.
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
The validate method will return with the info whether the data passed is validated against the structure and it will return
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
#### Helper method
We can also use helper method to get the `Amsify42\TypeStruct\TypeStruct` new instance.
```php
/**
 * If we have direct of the typestruct file
 */
$typeStruct = get_typestruct('/path/to/Simple.php');
$result = $typeStruct->validate($data);
```

```php
/**
 * For class, we need to pass full class name and 2nd param as 'class'
 */
$typeStruct = get_typestruct(App\TypeStruct\Simple::class, 'class');
$result = $typeStruct->validate($data);
```

#### Options
With Typestruct instance we can set these options before calling `validate()` method
```php
/**
 * To tell the typestruct that data we are passing is of type object(stdClass)
 * default is false
 */
$typeStruct->isDataObject(true);
/**
 * If true, it will validate and collect all error messages else it will get the first error and exit
 * Default is true
 */
$typeStruct->validateFull(false);
/**
 * Default is empty string, you can either pass 'json' or 'xml' based on the type of data you are passing for validation.
 */
$typeStruct->contentType('json');
/**
 * Absolute path to the typestruct file
 */
$typeStruct->setPath('/path/to/Sample.php');
/**
 * Full class name of typestruct file
 */
$typeStruct->setClass(App\TypeStruct\Simple::class);
```

### 3. Data
The data you can pass for validation are
```txt
Array
Object(stdClass)
Json
XML
```
As we have already seen the array example, lets see the examples for the rest
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
**Note:** We are passing `true` to method `isDataObject()` to tell **TypeStruct** that the data we are passing is of type **Object(stdClass)**. 

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
We can also use the other external **TypeStruct** file as a element
```php
export typestruct Category {
    id: int,
    name: string
}
```
Now we can use `Category` as type like this
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
or as array of this type
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
You can also attach more rules to the input like this
```php
namespace App;
export typestruct User {
    id: int,
    name: string,
    email: string<email>
}
```
As you can see, we have added rule `email` to the email element which will check for valid email address. You can add more rules to the element separated by dot `.` like this
```php
namespace App;
export typestruct User {
    id: int,
    url: string<url.checkHost>
}
```
These are the pre defined rules you can use
```txt
email - Check for valid email
url - Check if string is a valid url
date - Check if string is a valid date
```

### 6. Custom Rules
We can also write method to perform cutom validation but this can only be achieved when we create class and extends it to `Amsify42\TypeStruct\Validator`
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
<br/>
If you want to access data from custom method more easily, you can also use the method `$this->path()` which will directly get the element from multi level path.
```php
class Sample extends Validator
{
    ...
    protected $data = [
                        'id'    => 42,
                        'detail' => [
                            'more' => [
                                'location' => 'City'
                            ]
                        ]
                    ];

    public function checkCustom()
    {
        echo $this->path('detail.more.location'); /* It will print `City` */
    }                                 
} 
```
**Note:** `$this->path` expects parameters to be key name separated by dot(if multiple keys) and will either return `NULL`(if key does not exist) or the target key value.