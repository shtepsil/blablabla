<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 22.08.2022
 * Time: 22:15
 */

namespace common\models;

use Yii;

class OptUsers extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'opt_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'action_id', 'user_id'], 'required'],
            [['id', 'action_id', 'user_id'], 'integer']
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
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAction()
    {
        return $this->hasOne(Actions::className(), ['id' => 'action_id']);
    }
}

