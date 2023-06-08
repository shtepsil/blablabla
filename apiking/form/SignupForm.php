<?php

namespace apiking\form;

use common\models\User;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use common\validators\UserExist;

/**
 * Sign Up form
 */
class SignupForm extends Model
{
    const PASSWORD_LENGTH = 8;


    public $username;
	public $password;
    public $phone;
    public $email;
    public $role;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            // ['username', 'unique', 'targetClass' => User::class,
                // 'message' => 'Это имя уже занято.',
            // ],
            ['username', 'string', 'min' => 2, 'max' => 255],
			['password', 'string', 'min' => 2, 'max' => 255],
            ['phone', 'trim'],
            ['phone', 'required'],
            ['phone', UserExist::className()],
            ['phone', 'match', 'pattern' => '/[0-9]+$/i',
                'message' => 'Телефон должен содержать только цифры от 0 до 9.',
            ],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', UserExist::className()],
/*
            ['role', 'required'],
            ['role', 'in', 'range' => ['driver', 'advertiser']],*/
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->phone = $this->phone;
		$user->status = 10;
		$user->isEntity = 0;
  //      $password = Yii::$app->security->generateRandomString(self::PASSWORD_LENGTH);
        $user->setPassword($this->password);
        $user->generateAuthKey();
    //    $user->generateEmailVerificationToken();

        if ($user->save()) {
			 return $user->auth_key;
/*
            if ($this->sendEmail($user, $password)) {
                return $user->auth_key;
            }
            throw new \yii\web\HttpException(500, 'Problem with sending confirm mail.');
			*/
        }
     //   throw new \yii\web\HttpException(500, 'Problem with saving data.');
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @param String $password just generated user`s password
     * @return bool whether the email was sent
     */
    protected function sendEmail($user, $password)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user, 'password' => $password]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration | ' . Yii::$app->name)
            ->send();
    }
}
