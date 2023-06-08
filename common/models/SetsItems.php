<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sets_items".
 *
 * @property integer $id
 * @property integer $set_id
 * @property integer $item_id
 * @property integer $price
 * @property double $count
 *
 * @property Items $item
 * @property Sets $set
 */
class SetsItems extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sets_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['set_id', 'item_id'], 'required'],
            [['set_id', 'item_id', 'price'], 'integer'],
            [['count'], 'number'],
            [['count'], 'default','value'=>0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'set_id' => 'Сет',
            'item_id' => 'Товар',
            'price' => 'Цена',
            'count' => 'Количество в сете',
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
    public function getSet()
    {
        return $this->hasOne(Sets::className(), ['id' => 'set_id']);
    }
}
