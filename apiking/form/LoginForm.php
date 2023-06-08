<?php

namespace apiking\form;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends \common\models\LoginForm
{
    /**
     * @return Token|null
     */
    public function auth()
    {
        if ($this->validate()) {
            return $this->getUser()->auth_key;
        } else {
            return null;
        }
    }

    /**
     * Desktop authorization feature
     * @return bool
     */
    public function authDesktop()
    {
        if (!$this->validate()) {
            return false;
        }

        return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
    }
}
