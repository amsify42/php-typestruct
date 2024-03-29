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
7. [Complex Example](#7-complex-example)


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

#### Autoloading
Autoloading of the typestruct file will be done automatically if its name and path is based on *psr-4* standards else you need to use `setPath()` method with typestruct instance which expects direct path of the typestruct file.

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

namespace App\Validators;

use Amsify42\TypeStruct\Validator;

class Sample extends Validator
{
    protected $tsClass = \App\TypeStruct\Simple::class;

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
You can also set the data before validating against the typestruct like this
```php
$sample = new \Amsify42\Validators\Sample();
$sample->setData(['id' => 42, 'name' => 'amsify']);
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
 * You can set to json or xml, default is empty string
 */
protected $contentType;
/**
 * Tells the typestruct whether the data we setting/passing is of type Object(stdClass)
 */
protected $isDataObject;
```

### 5. Rules
#### Basic
These are the basic types we can use for elements. It will check whether key exist and its type.
```php
export typestruct Sample {
    id: int,
    name: string,
    price: float,
    points: numeric,
    is_active: boolean,
    is_public: tinyInt,
    items: array
    some: any
}
```
`numeric` work just like php `is_numeric()` method which allows numbers even in quotes. `tinyInt` expects the value to be either `0` or `1` and type `any` means that element value could be of any type.
#### Optional
To make the element optional, we simply prefix it with question mark **?**
```php
export typestruct Sample {
    id: int,
    name: string,
    email: ?string
}
```
Optional can also be applied to child dictionary
```php
export typestruct Sample {
    id: int,
    name: string,
    email: ?string,
    details : ?{
        address: string,
        pincode: ?int
    }
}
```
#### Length
We can also set the limits to the length of these types like this
```php
export typestruct Sample {
    id: int(5),
    name: string(20),
    price: float(5.2),
    is_active: boolean,
    items: [5]
}
```
#### Array
These are the array types we can use
```php
items: int[] 
items: string[]
items: float[]
items: numeric[]
items: boolean[]
items: tinyInt[]
```
#### External as Child
We can also use the other external **TypeStruct** file as a element
```php
namespace App\TypeStruct;
export typestruct Category {
    id: int,
    name: string
}
```
Now we can use `Category` as type like this
```php
namespace App\TypeStruct;
use App\TypeStruct\Category;
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
namespace App\TypeStruct;
use App\TypeStruct\Category;
export typestruct Product {
    id: int,
    name: string,
    price: float,
    active: boolean,
    categories: Category[]
}
```
#### More Rules
You can also attach more rules to the input like this
```php
namespace App\TypeStruct;
export typestruct User {
    id: int,
    name: string,
    email: string<email>
}
```
As you can see, we have added rule `email` to the email element which will check for valid email address. You can add more rules to the element separated by dot `.` like this
```php
namespace App\TypeStruct;
export typestruct User {
    id: int,
    url: string<url.checkHost>
}
```
These are the pre defined rules you can use
```txt
nonEmpty - Check for non empty value just like php method empty() checks
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

namespace App\Validators;

use Amsify42\TypeStruct\Validator;
use App\TypeStruct\Simple;

class Sample extends Validator
{
    protected $tsClass = Simple::class;

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
We can use `$this->name()` to get the name of current element and  `$this->value()` to get the value of the current element which is applicable to the rule. To get the other element value, we already have `$this->data` accessible from these custom rule methods.
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

### 7. Complex Example
```php
namespace App\TypeStruct;

use App\TypeStruct\User;

export typestruct Sample {
    name: string,
    email: string,
    is_test: tinyInt,
    id: int,
    address: {
        door: string,
        zip: int
    },
    items: [],
    user : User,
    someEl: {
        key1: string,
        key2: int,
        key12: array,
        records: \App\TypeStruct\Record[],
        someChild: {
            key3: boolean,
            key4: float,
            someAgainChild: {
                key5: string,
                key6: float,
                key56: boolean[]
            }
        }
    }
}
```
```php
<?php

namespace App\TypeStruct;

export typestruct User {
    id: int,
    name: string,
    email: string<email>
}
```
```php
<?php

namespace App\TypeStruct;

export typestruct Record {
    id: int,
    name: string
}
```
The above complex and multi level typestruct example file will be validated with the data:
```php
[
    'name' => 'amsify',
    'is_test' => '1',
    'user' => [
        'id' => 1,
        'name' => 'some',
        'email' => 'some@site.com'
    ],
    'address' => [
        'door' => '12-3-534',
        'zip' => 600035
    ],
    'url' => 'https://www.site.com/page.html',
    'items' => [1,2,3,4,5,6,7],
    'someEl' => [
        'key1' => 'val1',
        'key2' => 2,
        'key12' => [1,2,12],
        'records' => [
            [
                'id' => 1,
                'name' => 'r1'
            ],
            [
                'id' => 2,
                'name' => 'r2'
            ]
        ],
        'someChild' => [
            'key3' => true,
            'key4' => 4.01,
            'someAgainChild' => [
                'key5' => 'val5',
                'key6' => 6.4,
                'key56' => [true,false,true]
            ]
        ]
    ]
]
```