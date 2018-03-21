<?php
namespace paulzi\jsonBehavior;

use yii\base\InvalidParamException;
use yii\db\BaseActiveRecord;
use yii\validators\Validator;

class JsonValidator extends Validator
{
    /**
     * @var bool
     */
    public $merge = false;

    /**
     * Map json error constant to message
     * @see: http://php.net/manual/ru/json.constants.php
     * @var array
     */
    public $errorMessages = [];

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (!$value instanceof JsonField) {
            try {
                $new = new JsonField($value);
                if ($this->merge) {
                    /** @var BaseActiveRecord $model */
                    $old = new JsonField($model->getOldAttribute($attribute));
                    $new = new JsonField(array_merge($old->toArray(), $new->toArray()));
                }
                $model->$attribute = $new;
            } catch (InvalidParamException $e) {
                $this->addError($model, $attribute, $this->getErrorMessage($e));
                $model->$attribute = new JsonField();
            }
        }
    }

    /**
     * @param \Exception $exception
     * @return string
     */
    protected function getErrorMessage($exception)
    {
        $code = $exception->getCode();
        if (isset($this->errorMessages[$code])) {
            return $this->errorMessages[$code];
        }
        return $exception->getMessage();
    }
}
