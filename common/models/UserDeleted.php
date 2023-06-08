<?php

namespace common\models;

use common\components\Debugger as d;

class UserDeleted extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_deleted';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['sex'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ID удалённого пользователя (из таблицы user)',
            'username' => 'Имя пользователя',
            'email' => 'Email',
            'phone' => 'Телефон',
            'sex' => 'Пол',
            'deleted_at' => 'Дата удаления',
        ];
    }

}//Class