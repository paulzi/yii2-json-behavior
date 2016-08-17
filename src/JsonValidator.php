<?php
namespace paulzi\jsonBehavior;

use yii\base\InvalidParamException;
use yii\validators\Validator;

class JsonValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (!$value instanceof JsonField) {
            try {
                $model->$attribute = new JsonField($value);
            } catch (InvalidParamException $e) {
                $this->addError($model, $attribute, $e->getMessage());
                $model->$attribute = new JsonField();
            }
        }
    }
}
