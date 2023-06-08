<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "options_value".
 *
 * @property integer $id
 * @property integer $option_id
 * @property string $value
 *
 * @property ItemOptionsValue[] $itemOptionsValues
 * @property Options $option
 */
class OptionsValue extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'options_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['option_id', 'value'], 'required'],
            [['option_id'], 'integer'],
            [['value'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'option_id' => 'Option ID',
            'value' => 'Значение',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemOptionsValues()
    {
        return $this->hasMany(ItemOptionsValue::className(), ['option_value_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasOne(Options::className(), ['id' => 'option_id']);
    }
}
