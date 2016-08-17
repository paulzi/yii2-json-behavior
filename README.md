# Yii2 json attribute behavior

Auto decode/encode attribute value in json, provide array access and json validator.

[Russian readme](https://github.com/paulzi/yii2-json-behavior/blob/master/README.ru.md)

[![Packagist Version](https://img.shields.io/packagist/v/paulzi/yii2-json-behavior.svg)](https://packagist.org/packages/paulzi/yii2-json-behavior)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/paulzi/yii2-json-behavior/master.svg)](https://scrutinizer-ci.com/g/paulzi/yii2-json-behavior/?branch=master)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/paulzi/yii2-json-behavior.svg)](https://scrutinizer-ci.com/g/paulzi/yii2-json-behavior/?branch=master)
[![Build Status](https://img.shields.io/travis/paulzi/yii2-json-behavior/master.svg)](https://travis-ci.org/paulzi/yii2-json-behavior)
[![Total Downloads](https://img.shields.io/packagist/dt/paulzi/yii2-json-behavior.svg)](https://packagist.org/packages/paulzi/yii2-json-behavior)

## Install

Install via Composer:

```bash
composer require paulzi/yii2-json-behavior
```

or add

```bash
"paulzi/yii2-json-behavior" : "~1.0.0"
```

to the `require` section of your `composer.json` file.

## Usage

### JsonBehavior

Configure your model:

```php
use paulzi\jsonBehavior\JsonBehavior;

class Item extends \yii\db\ActiveRecord
{
    public function behaviors() {
        return [
            [
                'class' => JsonBehavior::className(),
                'attributes' => ['params'],
            ],
        ];
    }
}
```

Now you can access to attribute as array:

```php
$item = Item::findOne(1);
$item->params['one'] = 'two';
$item->params['two'] = [];
$item->params['two']['key'] = true;
$item->save();

$item = Item::findOne(1);
echo $item['two']['key']; // true
```

Set attribute via json string:

```php
$item = new Item();
$item->params->set('[2, 4, 42]');
echo $item->params[2]; // 42
```

Set attribute via array:

```php
$item = new Item();
$item->params->set(['test' => ['one' => 1]]);
echo $item->params['test']['one']; // 1
```

Convert to json string:

```php
$item = new Item();
$item->params['test'] = ['one' => false, 'two' => [1, 2, 3]];
var_dump((string)$item->params); // {"one":false,"two":[1,2,3]}
```

Convert to array:

```php
$item = new Item();
$item->params->set('{ "one": 1, "two": null, "three": false, "four": "four" }');
var_dump($item->params->toArray());
```

Check empty:

```php
$item = new Item();
$item->params->set('{}');
var_dump($item->params->isEmpty()); // true
```

### JsonValidator

Configure your model (see behavior config upper):

```php
use paulzi\jsonBehavior\JsonValidator;

class Item extends \yii\db\ActiveRecord
{
    public function rules() {
        return [
            [['params'], JsonValidator::className()],
        ];
    }
}
```

Validate:

```php
$item = new Item();
$item->attributes = ['params' => '{ test: }'];
var_dump($item->save()); // false
var_dump($item->errors); // ['params' => ['Value is not valid JSON or scalar']]
```

### JsonField

You can use `JsonField` class for other models:

```php
class Item
{
    public $params;
    
    public function __constructor()
    {
        $this->params = new JsonField();
    }
}

// ...

$item = new Item();
$item->params['one'] = 1;
var_dump((string)$item->params); // {"one":1}
```