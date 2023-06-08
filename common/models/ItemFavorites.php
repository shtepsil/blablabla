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
class ItemFavorites extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item_favorites';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'user_id'], 'required'],
            [['item_id', 'user_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_id' => 'Item ID',
            'user_id' => 'user_id'
        ];
    }
	
	/**
	* @return \yii\db\ActiveQuery
	*/
    public function getItem()
    {
        return $this->hasOne(Items::className(), ['id' => 'item_id']);
    }


}
