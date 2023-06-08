<?php
namespace frontend\form;

use common\models\User;
use yii\base\Model;

/**
 * Password reset request form
 */
class Recovery extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'Данный E-Mail не найдён'
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email'=>'Электронная почта',

        ];
    }
    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if ($user) {
            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }

            if ($user->save()) {
                \Yii::$app->mailer->compose(['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'], ['user' => $user])
                    ->setFrom([\Yii::$app->params['supportEmail'] => 'Интернет-магазин ' . \Yii::$app->params['siteName'] . '.kz'])
                    ->setTo($this->email)
                    ->setSubject('Восстановление пароля на сайте' . \Yii::$app->params['siteName'] . '.kz')
                    ->send();
                return true;
            }
        }

        return false;
    }
    public function send()
    {
        $result = [];
        if($this->sendEmail()){
            $result['message']['success'] = 'Ссылка для восстановления отправлена на E-Mail!';
            $result['js']=<<<JS
\$form.resetForm();
JS;
        }else{
            $result['message']['error'] = 'Произошла ошибка на стороне сервера!';
        }
        return $result;
    }
}
