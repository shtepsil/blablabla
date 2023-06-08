<?php

/**
 * Класс для работы с датами
 */

namespace common\helpers;

use common\components\Debugger as d;

class DateHelper
{

    public static $time = 0;

    /**
     * Пока что можем принимать либо цифры time, либо строку флага.
     * Можно ещё доработать чтобы определять строку даты.
     * Проверку сделать вместо is_numeric($data)
     * @param $data
     * @return array|mixed
     */
    public static function getDayOfWeek($data = 'all', $flag = false)
    {
        // Если $time не задано из вне.
        if(self::$time === 0){
            // Зададим time тут
            $str_time = time();
        }else{
            // Если $time было задано из вне.
            $str_time = self::$time;
        }
        // Если $data не одно из строк массива и это число
        if(!in_array($data, ['all', 'name', 'number']) AND is_numeric($data)){
            // Зададим time метода из $data
            $str_time = $data;
        }
        $day_eng = date ('l', $str_time);

        switch($day_eng){
            case 'Monday':
                $day = [
                    'name' => 'Понедельник',
                    'number' => 1
                ];
                break;
            case 'Tuesday':
                $day = [
                    'name' => 'Вторник',
                    'number' => 2
                ];
                break;
            case 'Wednesday':
                $day = [
                    'name' => 'Среда',
                    'number' => 3
                ];
                break;
            case 'Thursday':
                $day = [
                    'name' => 'Четверг',
                    'number' => 4
                ];
                break;
            case 'Friday':
                $day = [
                    'name' => 'Пятница',
                    'number' => 5
                ];
                break;
            case 'Saturday':
                $day = [
                    'name' => 'Суббота',
                    'number' => 6
                ];
                break;
            default:
                $day = [
                    'name' => 'Воскресенье',
                    'number' => 7
                ];
        }

        if($data == 'name'){ $day = $day['name']; }
        if($data == 'number'){ $day = $day['number']; }

        if($flag) d::ajax($day);
        return $day;
    }

    /**
     * Проверка выходных дней
     * @return bool
     */
    public static function weekendCheck()
    {
        $result = false;
        $current_day = self::getDayOfWeek('number');
        if(in_array($current_day, [6, 7])){
            $result = true;
        }
        return $result;
    }

    /**
     * Проверка выходных дней
     * @return bool
     */
    public static function currentDayStart()
    {
        return strtotime( date('Y-m-d' . ' 00:00:00') );
    }

}//Class