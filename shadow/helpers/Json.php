<?php

namespace shadow\helpers;

class Json extends \yii\helpers\Json
{
    // Проверка на Json
    public static function isJson($string) {
        return is_string($string) && (is_object(json_decode($string))
                || is_array(json_decode($string)));
    }

}//Class