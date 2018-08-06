# Yii2 json attribute behavior

Автоматически призводит кодирование/декодирование атрибутов в формат json, предоставляет доступ как к массиву и валидацию json.

**ВНИМАНИЕ! Начиная с версии 2.0.14 Yii имеет встроенную поддержку JSON-типов в БД, и данное поведение больше не будет работать с такими полями.**

[English readme](https://github.com/paulzi/yii2-json-behavior/)

[![Packagist Version](https://img.shields.io/packagist/v/paulzi/yii2-json-behavior.svg)](https://packagist.org/packages/paulzi/yii2-json-behavior)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/paulzi/yii2-json-behavior/master.svg)](https://scrutinizer-ci.com/g/paulzi/yii2-json-behavior/?branch=master)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/paulzi/yii2-json-behavior.svg)](https://scrutinizer-ci.com/g/paulzi/yii2-json-behavior/?branch=master)
[![Build Status](https://img.shields.io/travis/paulzi/yii2-json-behavior/master.svg)](https://travis-ci.org/paulzi/yii2-json-behavior)
[![Total Downloads](https://img.shields.io/packagist/dt/paulzi/yii2-json-behavior.svg)](https://packagist.org/packages/paulzi/yii2-json-behavior)

## Установка

Установка через Composer:

```bash
composer require paulzi/yii2-json-behavior
```

или добавьте

```bash
"paulzi/yii2-json-behavior" : "~1.0.0"
```

в секцию `require` в вашем `composer.json` файле.

## Использование

### JsonBehavior

Настройте вашу модель:

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

Теперь вы можете обращаться к атрибуту как к массиву:

```php
$item = Item::findOne(1);
$item->params['one'] = 'two';
$item->params['two'] = [];
$item->params['two']['key'] = true;
$item->save();

$item = Item::findOne(1);
echo $item['two']['key']; // true
```

Установка значения атрибута через JSON-строку:

```php
$item = new Item();
$item->params->set('[2, 4, 42]');
echo $item->params[2]; // 42
```

Установка значения атрибута через массив:

```php
$item = new Item();
$item->params->set(['test' => ['one' => 1]]);
echo $item->params['test']['one']; // 1
```

Получение значения в виде JSON-строки:

```php
$item = new Item();
$item->params['test'] = ['one' => false, 'two' => [1, 2, 3]];
var_dump((string)$item->params); // {"one":false,"two":[1,2,3]}
```

Получение значения в виде массива:

```php
$item = new Item();
$item->params->set('{ "one": 1, "two": null, "three": false, "four": "four" }');
var_dump($item->params->toArray());
```

Проверка на пустоту:

```php
$item = new Item();
$item->params->set('{}');
var_dump($item->params->isEmpty()); // true
```

#### emptyValue

Вы можете задать опцию `emptyValue` для определения значения для пустого JSON (по умолчанию `null`). Может принимать значения `'{}'`, `'[]''` или `null`.

### JsonValidator

Настройте дополнительно модель (подключение поведения описано выше):

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

Валидация:

```php
$item = new Item();
$item->attributes = ['params' => '{ test: }'];
var_dump($item->save()); // false
var_dump($item->errors); // ['params' => ['Value is not valid JSON or scalar']]
```

В качестве опции можно передать `merge = true`, в этом случае вместо замены всего значения поля переданными данными, будет осуществлен `array_merge()` со старыми данными в поле (которые берутся из `oldAttributes` ActiveRecord). Данный параметр применим только для ActiveRecord:
 
```php
use paulzi\jsonBehavior\JsonValidator;

class Item extends \yii\db\ActiveRecord
{
    public function rules() {
        return [
            [['params'], JsonValidator::className(), 'merge' => true],
        ];
    }
}
```

### JsonField

Вы можете использовать класс `JsonField` для других моделей:

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

## How To

### Использование методов isAttributeChanged() and getDirtyAttributes()

Yii2 не предоставляет возможности для внедрения стороннего кода при выполнении проверки `dirty` атрибута.

Если вам необходимо использовать методы `isAttributeChanged()` или `getDirtyAttributes()`, вы можете переопределить их в модели:

```php
/**
 * @inheritdoc
 */
public function isAttributeChanged($name, $identical = true)
{
    if ($this->$name instanceof JsonField) {
        return (string)$this->$name !== $this->getOldAttribute($name);
    } else {
        return parent::isAttributeChanged($name, $identical);
    }
}

/**
 * @inheritdoc
 */
public function getDirtyAttributes($names = null)
{
    $result = [];
    $data = parent::getDirtyAttributes($names);
    foreach ($data as $name => $value) {
        if ($value instanceof JsonField) {
            if ((string)$value !== $this->getOldAttribute($name)) {
                $result[$name] = $value;
            }
        } else {
            $result[$name] = $value;
        }
    }
    return $result;
}
```