<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 28.03.2022
 * Time: 15:47
 */

namespace apiking\components;

use common\components\Debugger as d;

class ApiResponse
{

    public static function success($content = 'Выполнено', $header = 'Успешно')
    {
        return [
            'error' => false,
            'status' => $content,
        ];
    }

    public static function error($content = 'Ошибка на сервере', $header = 'Внимание')
    {
        return [
            'error' => true,
            'status' => $content,
        ];
    }

}//Class