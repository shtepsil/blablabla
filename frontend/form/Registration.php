<?php

namespace frontend\form;

use common\components\Debugger as d;
use common\models\User;
use common\validators\UserExist;
use shadow\helpers\StringHelper;
use shadow\widgets\ReCaptcha\ReCaptchaValidator;
use yii\base\Model;
use yii\helpers\Json;

class Registration extends Model
{
    public $name;
    public $phone;
    public $email;
    public $password;
    public $verifyCode;
    /**
     * @inheritdoc 
     */
    public function rules()
    {
        return [
            [['name', 'email', 'phone', 'password'], 'trim'],
            [['phone'], 'match', 'pattern' => '/^((\+?7)(\(?\d{3})\)-?)?(\d{3})(-?\d{4})$/', 'message' => \Yii::t('main', 'Некорректный формат поля {attribute}')],
            [['name', 'email', 'phone', 'password'], 'required'],
            [
                ['password'],
                'match',
				'pattern' => '/^[A-Za-z0-9_!@#$%^&*()+=?.,-]+$/u',
                'message' => 'Не допустимые символы',
            ],
            [['password'], 'string', 'length' => [4, 255]],
            ['email', 'email'],
            ['email', UserExist::className()],
            ['phone', UserExist::className()],
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Код с картинки',
            'name' => 'Имя',
            'phone' => 'Телефон',
            'email' => 'E-Mail',
            'password' => 'Пароль',
        ];
    }

//    /**
//     * Sends an email to the specified email address using the information collected by this model.
//     *
//     * @param  string  $email the target email address
//     * @return boolean whether the email was sent
//     */
//    public function sendEmail($email)
//    {
//        return Yii::$app->mailer->compose()
//            ->setTo($email)
//            ->setFrom([$this->email => $this->name])
//            ->setSubject($this->subject)
//            ->setTextBody($this->body)
//            ->send();
//    }
    public function send()
    {
        $result = [];
        $record = new User();
        $record->isEntity = 0;
        $record->email = $this->email;
        $record->phone = $this->phone;
        $record->username = $this->name;
        $record->status = $record::STATUS_ACTIVE;
        $record->password = $this->password;
        $record->generateAuthKey();
        if ($record->save()) {
            \Yii::$app->user->login($record);
            if ($record->email) {
                \Yii::$app->function_system->send_promo_code('reg', $record->email);
            }
            $result['message']['success'] = 'Вы успешно зарегистрировались!';
            $result['js'] = <<<JS
location.reload();
JS;
        } else {
            //d::ajax($record->getErrors());
            $result['message']['error'] = 'Произошла ошибка на стороне сервера1!';
        }
        return $result;
    }
}