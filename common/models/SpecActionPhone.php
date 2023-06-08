<?php

namespace common\models;

use shadow\assets\Select2Assets;
use shadow\plugins\datetimepicker\DateTimePicker;
use shadow\widgets\CKEditor;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * This is the model class for table "spec_action_codes".
 *
 * @property int $id
 * @property int $status
 * @property int $spec_action_code_id [int(11)]
 * @property string $name [varchar(255)]
 * @property string $phone [varchar(255)]
 * @property string $code [varchar(255)]
 * @property int $send_time [int(11)]
 *
 *
 */
class SpecActionPhone extends \shadow\SActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'spec_action_phones';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spec_action_code_id','phone'], 'required'],
            [['uuid'], 'string', 'max' => 255],
            [['spec_action_code_id'], 'integer'],
            [['status'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',

        ];
    }
    public function generateCode()
    {
        $code = '';
        for ($i = 0; $i <= 5; $i++) {
            $code .= mt_rand(0, 9);
        }

        return $code;
    }
//    /**
//     * @return \yii\db\ActiveQuery
//     */
//    public function getItemsCounts()
//    {
//        return $this->hasMany(ItemsCount::className(), ['city_id' => 'id']);
//    }

}
