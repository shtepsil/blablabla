<?php

namespace apiking\controllers;

use common\components\Debugger as d;
use common\models\User;
use yii\rest\ActiveController;
use Yii;

class MainController extends ActiveController
{
    public function init()
    {
        if (!Yii::$app->user->isGuest) {
            // Настройка пользователя перед операциями с ценами
            User::$id = Yii::$app->user->id;
        }
        parent::init();
    }
}//Class
