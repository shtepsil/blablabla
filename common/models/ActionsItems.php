<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "actions_items".
 *
 * @property integer $id
 * @property integer $action_id
 * @property integer $item_id
 *
 * @property Items $item
 * @property Actions $action
 */
class ActionsItems extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'actions_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'action_id', 'item_id'], 'required'],
            [['id', 'action_id', 'item_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'action_id' => 'Action ID',
            'item_id' => 'Item ID',
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
    public function getAction()
    {
        return $this->hasOne(Actions::className(), ['id' => 'action_id']);
    }
}
