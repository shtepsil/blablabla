<?php
/**
 * Created by PhpStorm.
 * Project: yii2-cms
 * User: lxShaDoWxl
 * Date: 24.04.15
 * Time: 16:12
 */
namespace shadow\helpers;

class StringHelper extends \yii\helpers\StringHelper
{
    /**
     * Возвращает форматированую страку из RU в EN чаще всего нужно для генерации ссылки
     * @param string $string строка которую форматировать
     * @return string отформатированная строка
     */
    public static function TranslitRuToEn($string)
    {
        $replace = array(
            '\'' => '',
            '"' => '',
            '!' => '',
            '@' => '',
            '#' => '',
            '$' => '',
            '%' => '',
            '^' => '',
            '&' => '',
            '*' => '',
            '_' => '-',
            '=' => '',
            ',' => '',
            '.' => '',
            ';' => '',
            ':' => '',
            '?' => '',
            '/' => '-',
            '\\' => '',
            '>' => '',
            '`' => '',
            '<' => '',
            '(' => '',
            ')' => '',
            '[' => '',
            ']' => '',
            '{' => '',
            '}' => '',
            " " => "_",
            "'" => "",
            "\"" => "",
            "а" => "a",
            "А" => "a",
            "б" => "b",
            "Б" => "b",
            "в" => "v",
            "В" => "v",
            "г" => "g",
            "Г" => "g",
            "д" => "d",
            "Д" => "d",
            "е" => "e",
            "Е" => "e",
            "Ё" => "Yo",
            "ё" => "yo",
            "ж" => "zh",
            "Ж" => "zh",
            "з" => "z",
            "З" => "z",
            "и" => "i",
            "И" => "i",
            "й" => "y",
            "Й" => "y",
            "к" => "k",
            "К" => "k",
            "л" => "l",
            "Л" => "l",
            "м" => "m",
            "М" => "m",
            "н" => "n",
            "Н" => "n",
            "о" => "o",
            "О" => "o",
            "п" => "p",
            "П" => "p",
            "р" => "r",
            "Р" => "r",
            "с" => "s",
            "С" => "s",
            "т" => "t",
            "Т" => "t",
            "у" => "u",
            "У" => "u",
            "ф" => "f",
            "Ф" => "f",
            "х" => "h",
            "Х" => "h",
            "ц" => "c",
            "Ц" => "c",
            "ч" => "ch",
            "Ч" => "ch",
            "ш" => "sh",
            "Ш" => "sh",
            "щ" => "sch",
            "Щ" => "sch",
            "ъ" => "",
            "Ъ" => "",
            "ы" => "y",
            "Ы" => "y",
            "ь" => "",
            "Ь" => "",
            "э" => "e",
            "Э" => "e",
            "ю" => "yu",
            "Ю" => "yu",
            "я" => "ya",
            "Я" => "ya",
            "і" => "i",
            "І" => "i",
            "ї" => "yi",
            "Ї" => "yi",
            "є" => "e",
            "Є" => "e"
        );
        return $str = iconv("UTF-8", "UTF-8//IGNORE", strtr($string, $replace));
    }
    public static function mb_ucfirst($string, $enc = 'UTF-8')
    {
        if (!function_exists('mb_ucfirst')) {
            return mb_strtoupper(mb_substr($string, 0, 1, $enc), $enc) . mb_substr($string, 1, mb_strlen($string, $enc), $enc);
        } else {
            return mb_ucfirst($string, $enc);
        }
    }
    /**
     * Создание уникального кода из чисел
     * @param $n int число которое переводим в буквенное значение
     * @return string
     */
    public static function num2alpha($n)
    {
        $r = '';
        for ($i = 1; $n >= 0 && $i < 10; $i++) {
            $r = chr(0x41 + ($n % pow(26, $i) / pow(26, $i - 1))) . $r;
            $n -= pow(26, $i);
        }
        return $r;
    }
    /**
     * Обратный перевод строки в число
     * @param $a string Строка которую перевести в число
     * @return int|number
     */
    public static function alpha2num($a)
    {
        $r = 0;
        $l = strlen($a);
        for ($i = 0; $i < $l; $i++) {
            $r += pow(26, $i) * (ord($a[$l - $i - 1]) - 0x40);
        }
        return $r - 1;
    }

    public static function strReplace($string,$aReplace){
        if($aReplace!==null && is_array($aReplace)) {
            foreach ($aReplace as $search => $replace) {
                $string = str_replace($search, $replace, $string);
            }
        }
        return $string;
    }

    /**
     * Очищаем строку от HTML тегов и HTML сущностей
     * @param string $string
     * @return mixed|string
     */
    public static function clearHtmlString($string = ''){
        if($string != ''){
            // Удаляем все HTML теги
            $string = strip_tags($string);
            // Сначала все HTML сущности, которые делают пробел, заменяем на пробелыб
            $string = str_replace("&nbsp;",' ', $string);
            // Удаляем все HTML сущности, содержащие в себе только числа
            $string = preg_replace("|(&#[0-9]+?;)|",'', $string);
            /*
             * Нужно получить все HTML сущности, которые должны быть заменены на символы.
             * Нужно создать отдельный метод, в котором будет происходить замена
             * всех необходимых HTML сущностей на соответствующие им символы.
             */
//            $string = preg_replace("|([&a-zA-Z;]+?)|",'', $string);

            // Обрезаем по краям строки все пробелы
            $string = trim($string);
        }
        return $string;
    }

    /**
     * Очищаем строку от HTML тегов и HTML сущностей v2
     * @param string $string
     * @return mixed|string
     */
    public static function clearStringFromHtml($string = ''){
        if($string != ''){
            // Удаляем все HTML теги
            $string = strip_tags($string);
            // Сначала все HTML сущности, которые делают пробел, заменяем на пробелыб
            $string = str_replace("&nbsp;",' ', $string);
            // Удаляем все HTML сущности, содержащие в себе только числа
            $string = preg_replace("|(&#[0-9]+?;)|",'', $string);
            /*
             * Нужно получить все HTML сущности, которые должны быть заменены на символы.
             * Нужно создать отдельный метод, в котором будет происходить замена
             * всех необходимых HTML сущностей на соответствующие им символы.
             */
//            $string = preg_replace("|([&a-zA-Z;]+?)|",'', $string);

            // Обрезаем по краям строки все пробелы
            $string = trim($string);
        }
        return $string;
    }

    public static function cucfirst($string = '')
    {
        $result = '';
        if(is_string($string) AND $string != ''){
            $char = mb_strtoupper(substr($string,0,2), "utf-8"); // это первый символ
            $string[0] = $char[0];
            $string[1] = $char[1];
            $result = $string;
        }
        return $result;
    }

    public static function getPartStrByCharacter($url, $haracter, $code = false){

        switch($code){
            case 'start':
                $pos = strpos($url, $haracter);
                if($pos != '') $str = substr($url, 0, $pos);
                else $str = $url;
                break;
            case 'last':
                $pos = mb_strripos($url, $haracter);
                if($pos != '') $str = substr($url, 0, $pos);
                else $str = $url;
                break;
            case 'all_from_first':
                $pos = strpos($url, $haracter);
                if($pos != '') $str = substr($url, $pos+1);
                else $str = $url;
                break;
            case 'string_all_from_first':
                $pos = strpos($url, $haracter);
                if($pos != '') $str = substr($url, $pos - 1);
                else $str = $url;
                break;
            default:
                $revstr = strrev($url);
                $position = strpos($revstr, $haracter);
                $str_itog_rev = substr($revstr,0,$position);
                $str = strrev($str_itog_rev);
        }

        return $str;

    }// function getPartStrByCharacter(...)

}//Class