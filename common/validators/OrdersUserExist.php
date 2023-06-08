<?php

namespace common\validators;

use common\components\Debugger as d;
use yii\helpers\Url;
use yii\validators\Validator;
use common\models\User;

class OrdersUserExist extends Validator
{

    public function validateAttribute($model, $attribute)
    {}

    public function clientValidateAttribute($model, $attribute, $view)
    {
        if (\Yii::$app->id == 'app-backend') {
            $searchValue = '';
            switch ($attribute) {
                case 'user_phone':
                    $searchValue = 'phone';
                    break;
                case 'user_mail':
                    $searchValue = 'email';
                    break;
            }

            if ($searchValue != '') {
                $searchValues = json_encode(User::find()->select($searchValue)->asArray()->column());
                $message = json_encode($this->message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                switch ($attribute) {
                    case 'user_phone':
                        $js = <<<JS
var noticePhone = $('.user-phone-exists');
var phoneExp = new RegExp(/\+\d\(\d{3}\)-\d{3}-\d{4}/, 'i');
noticePhone.html('').attr('data-value', '').attr('data-state', '');
// Валидация номера телефона
if(!phoneExp.test(value)){
    messages.push('Номер телефона введён не корректно');
    noticePhone.html('');
    return;
}
if ($.inArray(value, $searchValues) !== -1) {
    noticePhone.html($message).attr('data-value', value).attr('data-state', 'exists');
}else{
    noticePhone.html('Номер телефона не занят').attr('data-state', 'new');
}

JS;
                        break;
                    case 'user_mail':
                        $js = <<<JS
if(value !== ''){
    var noticeMail = $('.user-mail-exists');
    noticeMail.html('').attr('data-value', '').attr('data-state', '');
    var emailExp = new RegExp(/(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/, 'i');
    // Валидация email
    if(!emailExp.test(value)){
        messages.push('Email введён не корректно');
        noticeMail.html('');
        return;
    }
    if ($.inArray(value, $searchValues) !== -1) {
        noticeMail.html($message).attr('data-value', value).attr('data-state', 'exists');
    }else{
        noticeMail.html('Указанный Email не занят').attr('data-state', 'new');
    }
}else{
    $('.user-mail-exists').html('').attr('data-value', '').attr('data-state', '');
}
JS;
                        break;
                }

                return $js;
            }
        }
    }

} //Class