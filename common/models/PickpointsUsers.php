<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pickpoints_users".
 *
 * @property integer $id
 * @property integer $pickpoint_id
 * @property integer $user_id
 *
 * @property Pickpoint $pickpoint
 * @property SUser $suser
 */
class PickpointsUsers extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pickpoints_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pickpoint_id', 'user_id'], 'required'],
            [['id', 'pickpoint_id', 'user_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pickpoint_id' => 'pickpoint ID',
            'user_id' => 'user ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPickpoints()
    {
        return $this->hasOne(Pickpoint::className(), ['id' => 'pickpoint_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSUsers()
    {
        return $this->hasOne(SUsers::className(), ['id' => 'user_id']);
    }
}
