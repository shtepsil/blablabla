<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 28.09.2022
 * Time: 16:43
 */

namespace common\validators;

use common\components\Debugger as d;
use yii\helpers\Url;
use yii\validators\Validator;
use common\models\User;

class UserExist extends Validator
{

    /**
     * Проверка пользователя на существование,
     * с учётом статуса (активен/удалён), только для новой записи.
     * @param $attribute
     * @param $params
     */
    public function validateAttribute($model, $attribute)
    {
        if ($model->isNewRecord) {
            $user = User::find()
                ->where([$attribute => $model->$attribute])
                ->andWhere(['status' => User::STATUS_ACTIVE])
                ->one();
            if($user){
                $model->addError(
                    $attribute,
                    'Пользователь со значением (' . $model->$attribute . ') уже существует'
                );
            }
        }
    }

} //Class