<?php
/**
 * @link https://github.com/paulzi/yii2-json-behavior
 * @copyright Copyright (c) 2015 PaulZi <pavel.zimakoff@gmail.com>
 * @license MIT (https://github.com/paulzi/yii2-json-behavior/blob/master/LICENSE)
 */

namespace paulzi\jsonBehavior\tests;

use tests\Item;
use tests\TestMigration;
use paulzi\jsonBehavior\JsonField;

/**
 * @author PaulZi <pavel.zimakoff@gmail.com>
 */
class BehaviorTest extends \PHPUnit_Framework_TestCase
{
    public function testInitEmpty()
    {
        $model = new JsonField();
        $this->assertSame((string)$model, '');

        $model = new JsonField('');
        $this->assertSame((string)$model, '');

        $model = new JsonField(null);
        $this->assertSame((string)$model, '');

        $model = new JsonField([]);
        $this->assertSame((string)$model, '');
    }

    public function testInitString()
    {
        $model = new JsonField('{ "test": false }');
        $this->assertSame((string)$model, '{"test":false}');

        $model = new JsonField('{ "test": [1, 2, 3] }');
        $this->assertSame((string)$model, '{"test":[1,2,3]}');

        $model = new JsonField('{ "test": { "best": true}, "best": 2 }');
        $this->assertSame((string)$model, '{"test":{"best":true},"best":2}');

        $model = new JsonField('[1, false, "test", null]');
        $this->assertSame((string)$model, '[1,false,"test",null]');
    }

    /**
     * @expectedException \yii\base\InvalidParamException
     */
    public function testInitStringParseException()
    {
        $model = new JsonField('{test:}');
    }

    /**
     * @expectedException \yii\base\InvalidParamException
     */
    public function testInitStringNumberScalarException()
    {
        $model = new JsonField('0');
    }

    /**
     * @expectedException \yii\base\InvalidParamException
     */
    public function testInitStringNullScalarException()
    {
        $model = new JsonField('null');
    }

    /**
     * @expectedException \yii\base\InvalidParamException
     */
    public function testInitStringFalseScalarException()
    {
        $model = new JsonField('false');
    }

    /**
     * @expectedException \yii\base\InvalidParamException
     */
    public function testInitStringTrueScalarException()
    {
        $model = new JsonField('true');
    }

    public function testInitArray()
    {
        $model = new JsonField(['test' => false]);
        $this->assertSame((string)$model, '{"test":false}');

        $model = new JsonField(['test' => [1, 2, 3]]);
        $this->assertSame((string)$model, '{"test":[1,2,3]}');

        $model = new JsonField([1, false, "test", null]);
        $this->assertSame((string)$model, '[1,false,"test",null]');
    }

    /**
     * @expectedException \yii\base\InvalidParamException
     */
    public function testInitArrayException()
    {
        $model = new JsonField(new \stdClass());
    }

    public function testSetString()
    {
        $model = new JsonField();

        $model->set('{ "test": false }');
        $this->assertSame((string)$model, '{"test":false}');

        $model->set('{ "test": [1, 2, 3] }');
        $this->assertSame((string)$model, '{"test":[1,2,3]}');

        $model->set('{ "test": { "best": true}, "best": 2 }');
        $this->assertSame((string)$model, '{"test":{"best":true},"best":2}');

        $model->set('[1, false, "test", null]');
        $this->assertSame((string)$model, '[1,false,"test",null]');
    }

    /**
     * @expectedException \yii\base\InvalidParamException
     */
    public function testSetStringParseException()
    {
        $model = new JsonField();
        $model->set('{test:}');
    }

    /**
     * @expectedException \yii\base\InvalidParamException
     */
    public function testSetStringNumberScalarException()
    {
        $model = new JsonField();
        $model->set('0');
    }

    /**
     * @expectedException \yii\base\InvalidParamException
     */
    public function testSetStringNullScalarException()
    {
        $model = new JsonField();
        $model->set('null');
    }

    /**
     * @expectedException \yii\base\InvalidParamException
     */
    public function testSetStringFalseScalarException()
    {
        $model = new JsonField();
        $model->set('false');
    }

    /**
     * @expectedException \yii\base\InvalidParamException
     */
    public function testSetStringTrueScalarException()
    {
        $model = new JsonField();
        $model->set('true');
    }

    public function testSetArray()
    {
        $model = new JsonField();

        $model->set(['test' => false]);
        $this->assertSame((string)$model, '{"test":false}');

        $model->set(['test' => [1, 2, 3]]);
        $this->assertSame((string)$model, '{"test":[1,2,3]}');

        $model->set(['test' => ['best' => true], 'best' => 2]);
        $this->assertSame((string)$model, '{"test":{"best":true},"best":2}');

        $model->set([1, false, "test", null]);
        $this->assertSame((string)$model, '[1,false,"test",null]');
    }

    /**
     * @expectedException \yii\base\InvalidParamException
     */
    public function testSetArrayException()
    {
        $model = new JsonField(new \stdClass());
    }

    public function testArrayObjectAccess()
    {
        $model = new JsonField('{ "one": { "test": true }, "two": 2, "three": [1, 2, 3], "four": "4" }');
        $this->assertSame($model['one'], ['test' => true]);
        $this->assertSame($model['one']['test'], true);
        $this->assertSame($model['two'], 2);
        $this->assertSame($model['three'][2], 3);
        $this->assertSame($model['four'], '4');

        $model['one']['test'] = false;
        $model['two'] = 3;
        $model['three'][2] = 0;
        $model['four'] = null;
        $this->assertSame((string)$model, '{"one":{"test":false},"two":3,"three":[1,2,0],"four":null}');
    }

    public function testArrayArrayAccess()
    {
        $model = new JsonField('[1, false, "test", null]');
        $this->assertSame($model[0], 1);
        $this->assertSame($model[1], false);
        $this->assertSame($model[2], 'test');
        $this->assertSame($model[3], null);

        $model[0] = 2;
        $model[1] = true;
        $model[2] = 'best';
        $model[3] = ['test' => 'test'];
        $this->assertSame((string)$model, '[2,true,"best",{"test":"test"}]');
    }

    public function testToArray()
    {
        $model = new JsonField('{ "test": false }');
        $this->assertSame($model->toArray(), ['test' => false]);

        $model = new JsonField('{ "test": [1, null, 3] }');
        $this->assertSame($model->toArray(), ['test' => [1, null, 3]]);

        $model = new JsonField('{ "test": { "best": true}, "best": 2 }');
        $this->assertSame($model->toArray(), ['test' => ['best' => true], 'best' => 2]);

        $model = new JsonField('[1, false, "test", null]');
        $this->assertSame($model->toArray(), [1, false, "test", null]);
    }

    public function testIsEmpty()
    {
        $model = new JsonField();
        $this->assertSame($model->isEmpty(), true);

        $model->set('{}');
        $this->assertSame($model->isEmpty(), true);

        $model->set('[]');
        $this->assertSame($model->isEmpty(), true);

        $model->set('[false]');
        $this->assertSame($model->isEmpty(), false);

        $model->set('[0]');
        $this->assertSame($model->isEmpty(), false);

        $model->set('[[]]');
        $this->assertSame($model->isEmpty(), false);
    }

    public function testBehavior()
    {
        $item = new Item();
        $item->params['one'] = 'value';
        $item->params['two'] = [];
        $item->params['two']['test'] = true;
        $this->assertSame($item->save(false), true);
        $item->params['one'] = 42;
        $this->assertSame($item->params['one'], 42);

        $item = Item::findOne($item->id);
        $this->assertSame($item->params['one'], 'value');
        $this->assertSame($item->params['two']['test'], true);
    }

    public function testValidatorTest()
    {
        $item = new Item();

        $item->attributes = ['params' => '{"json": true}'];
        $this->assertSame($item->validate(), true);

        $item->attributes = ['params' => ['json' => true]];
        $this->assertSame($item->validate(), true);

        $item->attributes = ['params' => '{json:}'];
        $this->assertSame($item->validate(), false);
        $this->assertArrayHasKey('params', $item->errors);

        $item->attributes = ['params' => 'true'];
        $this->assertSame($item->validate(), false);
        $this->assertArrayHasKey('params', $item->errors);
    }

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        (new TestMigration())->up();
        parent::setUpBeforeClass();
    }
}