<?php
namespace frontend\form;

use common\models\User;
use yii\base\Model;

class RegistrationPage extends Model
{
    public $name;
    public $email;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email'], 'trim'],
            [['name', 'email',], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::className(), 'targetAttribute' => 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'email' => 'E-Mail',
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
        $record->username = $this->name;
        $record->status = $record::STATUS_DELETED;
        $record->password =  \Yii::$app->security->generateRandomString(6);
        $record->generateAuthKey();
        if ($record->save()) {
//            \Yii::$app->user->login($record);
//            if ($record->email) {
//                \Yii::$app->function_system->send_promo_code('reg', $record->email);
//            }
            $result['message']['success'] = 'Вы успешно зарегистрировались!';
            $result['js'] = <<<JS
\$form.resetForm();
JS;
        } else {
            $result['message']['error'] = 'Произошла ошибка на стороне сервера!';
        }
        return $result;
    }
}