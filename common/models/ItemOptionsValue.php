<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "item_options_value".
 *
 * @property integer $id
 * @property integer $item_id
 * @property integer $option_id
 * @property integer $option_value_id
 * @property string $value
 *
 * @property Items $item
 * @property Options $option
 * @property OptionsValue $optionValue
 */
class ItemOptionsValue extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item_options_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'option_id'], 'required'],
            [['item_id', 'option_id', 'option_value_id'], 'integer'],
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
            'item_id' => 'Товар',
            'option_id' => 'Характеристика',
            'option_value_id' => 'Значение фильтра из списка',
            'value' => 'Значение фильтра',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Items::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasOne(Options::className(), ['id' => 'option_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptionValue()
    {
        return $this->hasOne(OptionsValue::className(), ['id' => 'option_value_id']);
    }
}
