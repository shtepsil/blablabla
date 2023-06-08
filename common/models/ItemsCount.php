<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "items_count".
 *
 * @property integer $id
 * @property integer $item_id
 * @property integer $city_id
 * @property string $count
 *
 * @property Items $item
 * @property City $city
 */
class ItemsCount extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'items_count';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'item_id', 'city_id', 'count'], 'required'],
            [['id', 'item_id', 'city_id'], 'integer'],
            [['count'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id' => 'Item ID',
            'city_id' => 'City ID',
            'count' => 'Count',
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
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }
}
