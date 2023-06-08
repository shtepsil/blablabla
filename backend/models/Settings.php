<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "s_settings".
 *
 * @property integer $id
 * @property string $group
 * @property string $key
 * @property string $value
 */
class Settings extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 's_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group', 'key'], 'required'],
            [['value'], 'string'],
            [['group', 'key'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group' => 'Group',
            'key' => 'Key',
            'value' => 'Value',
        ];
    }


}
