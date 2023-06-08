<?php
namespace frontend\form;

use common\models\User;
use yii\base\Model;

class PromoEnterCode extends Model
{
    public $sms_code;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sms_code'], 'trim'],
            [['sms_code'], 'required'],
            [['sms_code'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sms_code' => 'SMS Код',
        ];
    }
}