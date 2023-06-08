<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "options_category".
 *
 * @property integer $id
 * @property integer $cid
 * @property integer $option_id
 * @property integer $isFilter
 *
 * @property Category $c
 * @property Options $option
 */
class OptionsCategory extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'options_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid', 'option_id'], 'required'],
            [['cid', 'option_id', 'isFilter'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cid' => 'Cid',
            'option_id' => 'Характеристика',
            'isFilter' => 'Использовать как фильтр',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getC()
    {
        return $this->hasOne(Category::className(), ['id' => 'cid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasOne(Options::className(), ['id' => 'option_id']);
    }
}
