<?php
namespace tests;

use paulzi\jsonBehavior\JsonValidator;
use yii\db\ActiveRecord;
use paulzi\jsonBehavior\JsonField;
use paulzi\jsonBehavior\JsonBehavior;

/**
 * @property integer $id
 * @property JsonField $params
 *
 * @method static Item|null findOne($condition)
 * @method static Item[] findAll($condition)
 */
class ItemRequired extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%item}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'      => JsonBehavior::className(),
                'attributes' => ['params'],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['params'], 'required'],
            [['params'], JsonValidator::className()],
        ];
    }
}