<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "item_associated".
 *
 * @property integer $id
 * @property integer $item_id_main
 * @property integer $item_id_sub
 *
 * @property Items $itemIdSub
 * @property Items $itemIdMain
 */
class ItemAssociated extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item_associated';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id_main', 'item_id_sub'], 'required'],
            [['item_id_main', 'item_id_sub'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id_main' => 'Item Id Main',
            'item_id_sub' => 'Item Id Sub',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemIdSub()
    {
        return $this->hasOne(Items::className(), ['id' => 'item_id_sub']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemIdMain()
    {
        return $this->hasOne(Items::className(), ['id' => 'item_id_main']);
    }
}
