<?php
/*
 * Этот класс ни где на сайте не задействован и его методы ни где не используются!
 * Я написал этот класс только для тестирования процесса разработки.
 * Поиск неисправностей, вывод информации ошибок и прочее.
 * Если интеренсно - можешь глянуть, что тут имеется.
 */

namespace common\components;

use Yii;
use shadow\helpers\SArrayHelper;
use yii\helpers\Html;

class Debugger
{
    public static $flag = false;
    public static $get_url = false;
    public static $get_data = false;
    public static $get_headers = false;
    public static $curl = false;
    public static $view_response = false;
    public $ip_debug_detect = 0;

    public function __construct()
    {
        if(defined ('DEBUG_DETECT') AND DEBUG_DETECT){
            /*
         * Все сайты, которые есть в массиве $debug_sites_allowed,
         * будут отображать debug скрипты,
         * если DEBUG_IP_DETECT пустая строка.
         */
            $debug_sites_allowed = [
                'test.kingfisher.kz'
            ];

            /*
             * Строка условия:
             * !in_array($_SERVER['SERVER_NAME'], ['kingfisher.kz'])
             * полностью вырубает весь if,
             * если сайт находится на kingfisher.kz и нужно использовать debug по IP,
             * то эту строку нужно закомментировать.
             */
            if(
                defined ('DEBUG_IP_DETECT')
                AND (DEBUG_IP_DETECT != '' OR $_SERVER['REMOTE_ADDR'] == '127.0.0.1')
                AND !is_bool(DEBUG_IP_DETECT)
                AND DEBUG_IP_DETECT !== 0 AND DEBUG_IP_DETECT !== 1
                AND ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' OR $_SERVER['REMOTE_ADDR'] == DEBUG_IP_DETECT)
                AND (!in_array($_SERVER['SERVER_NAME'], ['kingfisher.kz']) OR DEBUG_KINGFISHER_DETECT)
            ){
                /*
                 * Если DEBUG_IP_DETECT задан как пустая строка и это локальный IP,
                 * то нужно чтобы DEBUG_IP_DETECT работал на локалке, а для этого он не должен быть пустым,
                 * зададим туда просто 1.
                 * ========================================================================================
                 * В общем если DEBUG_IP_DETECT пустая строка - то на локалке всё должно работать.
                 * А иначе, значение для $this->ip_debug_detect, берём из DEBUG_IP_DETECT.
                 */
                if(DEBUG_IP_DETECT == '' AND $_SERVER['REMOTE_ADDR'] == '127.0.0.1'){
                    $this->ip_debug_detect = 1;
                }else{
                    $this->ip_debug_detect = DEBUG_IP_DETECT;
                }
            }else{
                /*
                 * Если DEBUG_IP_DETECT пуст и это не локальный сервер, то
                 * debug скрипты будут работать на всех сайтах, указанных в массиве $debug_sites_allowed,
                 * кроме kingfisher.kz
                 */
                if(
                    in_array($_SERVER['SERVER_NAME'], $debug_sites_allowed)
                    AND $_SERVER['SERVER_NAME'] != 'kingfisher.kz'
                ){
                    $this->ip_debug_detect = 1;
                }
            }

            // Принудительное включение debug скриптов
            if(
                defined ('DEBUG_DETECT_ALL')
                AND (
                    (is_bool(DEBUG_DETECT_ALL) AND DEBUG_DETECT_ALL)
                    OR (DEBUG_DETECT_ALL == 1)
                )
            ){
                $this->ip_debug_detect = DEBUG_DETECT_ALL;
            }
        }

        define('DEBUG_AJAX', $this->ip_debug_detect);
        define('DEBUG_PRI', $this->ip_debug_detect);
        define('DEBUG_PRE', $this->ip_debug_detect);
        define('DEBUG_PEX', $this->ip_debug_detect);
        define('DEBUG_PE', $this->ip_debug_detect);
        define('DEBUG_RN', $this->ip_debug_detect);

    }

    private static function s(){
        echo '<div
        class="pre"
        style="
            position: fixed;
            top: 60px;
            left: 0px;
            padding: 15px;
            background-color: #1F973D;
            min-width: 265px;
            z-index: 9999999999999999999999;
            color: white;
            font-size: 14px;
            overflow: auto;
        ">
        ';
    }

    private static function sbl(){
        echo '<div
          style="
            position: fixed;
            bottom: 5px;
            left: 0px;
            padding: 15px;
            background-color: #1F973D;
            min-width: 265px;
            z-index: 9999999999999999999999;
            color: white;
            font-size: 14px;
            overflow: auto;
          ">
        ';
    }

    private static function s_tr(){
        echo '<div
          style="
            position: fixed;
            top: 60px;
            right: 0px;
            padding: 15px;
            background-color: #1F973D;
            min-width: 265px;
            z-index: 9999999999999999999999;
            color: white;
            font-size: 14px;
            overflow: auto;
          ">
        ';
    }

    private static function s_br(){
        echo '<div
          style="
            position: fixed;
            bottom: 5px;
            right: 0px;
            padding: 15px;
            background-color: #1F973D;
            min-width: 265px;
            z-index: 9999999999999999999999;
            color: white;
            font-size: 14px;
            overflow: auto;
          ">
        ';
    }

    private static function e(){
        echo '</div>';
    }

    /*
     * Скрипты для разработки
     */
    public static function hpri($arr){
        header('Content-Type: text/html; charset=utf-8');
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }
    public static function pri($arr){
        if(!DEBUG_PRI) return false;
        echo '<pre style="font: 10pt/12pt Arial;">';
        print_r($arr);
        echo '</pre>';
    }
    public static function fpri($data){
        if(self::$flag){
            self::pri($data);
        }
    }
    public static function jpri($arr){
        // JSON_UNESCAPED_UNICODE
        $json = json_encode($arr, JSON_PRETTY_PRINT);
        printf("<pre style=\"font: 10pt/12pt Arial;\">%s</pre>", $json);
    }
    public static function pre($str){
        if(!DEBUG_PRE){
            return false;
        }
        self::s();
        echo '<pre>';
        print_r($str);
        echo '</pre>';
        self::e();
    }

    public static function prebl($str){
        self::sbl();
        echo '<pre>';
        print_r($str);
        echo '</pre>';
        self::e();
    }

    public static function pretr($str){
        self::s_tr();
        echo '<pre>';
        print_r($str);
        echo '</pre>';
        self::e();
    }

    public static function prebr($str){
        self::s_br();
        echo '<pre>';
        print_r($str);
        echo '</pre>';
        self::e();
    }

    // Для ajax ответов
    public static function res2($btn=false,$it=false){
        $html = '';

        if($btn !== false){
            $b_value = 'Нажать';
            $b_options = [
                'class'=>'btn btn-primary btn-xs',
            ];

            if(!is_array($btn)){
                $b_name = $btn;
            }else{
                if(count($btn)){

                    if(isset($btn['class'])){
                        Html::addCssClass($b_options,$btn['class']);
                    }

                    $b_options = ArrayHelper::merge($btn,$b_options);

                }
                $b_name = $b_options[0];
                $b_value = ($b_options['value'])?:$b_value;
                unset($b_options[0]);
                unset($b_options['value']);
            }

            $html .= Html::input('button',$b_name,$b_value,$b_options).'<br><br>';
        }

        if($it !== false){
            if(!$b_name) $b_name = 'test';
            $i_options = [
                'class'=>'i-'.$b_name,
            ];

            if(isset($it['options']) AND count($it['options'])){
                $i_options = ArrayHelper::merge($i_options,$it['options']);
            }

            $i_type = ($it['type'])?:'text';
            $i_name = ($it['name'])?:'i_'.$b_name;
            $i_value = ($it['value'])?:'';

            $input = Html::input($i_type,$i_name,$i_value,$i_options);
            $html = $input.$html;
        }

        $html .= '<div class="res">result</div>';
        return $html;
    }

    // Для ajax ответов
    public static function res($btn = false, $res_class = 'res'){
        $html = '<div style="font-size:18px;">';

        if($btn !== false){
            $html .= '<button type="button" name="'.$btn.'" class="btn btn-primary btn-xs" style="position:relative;"><img src="/images/animate/loading.gif" class="loading" style="position: absolute;top: -2px;left: -40px;display:none;" />Нажать</button><br><br>';
        }

        $html .= '<div class="' . $res_class . '">result</div></div>';
        return $html;
    }

    /*
     * Возврат json строки
     * для отладки
    */
    public static function eje($arr) {
        echo json_encode($arr, 256);
        exit();
    }

    /*
     * Возврат json строки
     * для отладки
    */
    public static function pj($arr) {
        print_r(json_encode($arr, 256));
    }

    /*
     * Распечатка массива
     * для отладки в Ajax
    */
    public static function pe($arr) {
        if(!DEBUG_PE) return false;
        echo '<br>';
        echo self::toString($arr);
        exit();
    }

    /*
     * Распечатка массива
     * для отладки в Ajax
    */
    public static function hpe($arr) {
        header('Content-Type: text/html; charset=utf-8');
        echo '<br>';
        echo self::toString($arr);
        exit();
    }

    /*
     * Распечатка массива
     * для отладки в Ajax
    */
    public static function pex($arr) {
        if(!DEBUG_PEX) return false;
        echo '<pre>';
        print_r($arr);
        exit('</pre>');
    }
    public static function fpex($data){
    if(self::$flag){
        self::pri(self::pex($data));
    }
}

    /*
     * Распечатка массива
     * для отладки в Ajax
    */
    public static function jpe($arr) {
        $arr = json_encode($arr, 256);
        print_r($arr);
        exit();
    }

    public static function arrToStr($data) {
        $str = '';
        $i = 0;
        if(is_array($data) OR is_object($data)){
            foreach($data as $key=>$value){
                if(is_array($value) OR is_object($value)){
                    $str .= $key.'=='.self::arrToStr($value).' ';
                }else {
                    $str .= (($i == 0) ? '>' : '').$key.'=>'.$value.', ';
                }
                $i++;
            }
        }else $str = $data;

        return $str;
    }

    public static function tdArrStr($data) {
        $str = self::arrToStr($data);
        file_put_contents('debug.txt',$str);
    }

    /*
     * Запись результатов в файл debug.txt
     * для отладки в Ajax
    */
    public static function td($data) {
        file_put_contents('debug.txt',$data);
    }
    public static function ftd($data)
    {
        if(self::$flag){
            self::td($data);
        }
    }

    /*
     * Запись результатов в файл debug.txt
     * для отладки в Ajax
    */
    public static function tdfa($data,$file = 'debug.txt') {
        file_put_contents($file,PHP_EOL.$data,FILE_APPEND);
    }
    public static function ftdfa($data)
    {
        if(self::$flag){
            self::tdfa($data);
        }
    }

    /*
     * Запись результатов в файл debug.txt
     * для отладки в Ajax
    */
    public static function jtd($data) {
        $data = json_encode($data, 256);
        file_put_contents('debug.txt', $data);
    }

    /*
     * Запись результатов в файл debug.txt
     * для отладки в Ajax
    */
    public static function fjtd($data) {
        if(self::$flag){
            self::jtd($data);
        }
    }

    /*
     * Запись результатов в файл debug.txt
     * для отладки в Ajax
    */
    public static function jtdfa($data) {
        $data = json_encode($data, 256);
        file_put_contents('debug.txt',PHP_EOL.$data,FILE_APPEND);
    }

    /*
     * Запись результатов в файл debug.txt
     * для отладки в Ajax
    */
    public static function fjtdfa($file,$data) {
        if(self::$flag){
            self::jtdfa($data);
        }
    }

    /*
     * Получить debug.txt
    */
    public static function getDebug($file = 'debug.txt') {
        if(file_exists($file)){
            $debug = file_get_contents($file);
        }else{
            $debug = 'Файл ' . $file . ' не существует';
        }
        return $debug;
    }

    /*
     * Получить debug.txt
    */
    public static function clearDebug($file = 'debug.txt') {
        if(file_exists($file)){
            file_put_contents($file, '');
            $result = $file.' очищен';
        }else{
            $result = 'Файл ' . $file . ' не существует';
        }
        return $result;
    }

    /*
     * Преобразвание массива в строку
     * для отладки в Ajax
    */
    public static function strpe($arr,$field=false) {
        $str = '<br>';
        foreach($arr as $key=>$value){
            if($field) $str .= $key.'=>'.$value[$field].'<br>';
            else $str .= $key.'=>'.$value.'<br>';
        }
        print_r($str);
        exit();
    }

    // Проверка на Json
    public static function isJson($string) {
        return ((is_string($string) &&
            (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }

    // Делаем строку из массива/объекта
    public static function toString($data){
        $str = '';
        $i = 0;
        if(is_array($data) OR is_object($data)){
            foreach($data as $key=>$value){
                if(is_array($value) OR is_object($value)){
                    $str .= '<br>' .
                        '<span style=\'color: red;\'>'.$key.'</span>'.
                        '<span style=\'font-weight:bold;\'>==</span><span style=\'color: blue;\'>'.
                        self::toString($value).'</span>'.' ';
                }else {
                    $str .=
                        (($i == 0) ? '>' : '') .
                        '<span style=\'color: red;\'>'.$key.'</span>'.
                        '=><span style=\'color: blue;\'>'.$value.'</span>'.', ';
                }
                $i++;
            }
        }else $str = '<span style=\'color: blue;\'>'.$data.'</span>';
        return $str.'<br>';
    }

    // Ответ Ajax запроса
    public static function echoAjax($data){
//        header('Accept: application/json');
        header("Content-type: application/json");
        echo json_encode($data, 256);
        exit();
    }

    public static function ajax($str = '', $key_response = 'response'){
        if(!DEBUG_AJAX){ return false; }
        if(self::isJson($str)){
            $str = json_decode($str, true);
        }
        if(is_object($str)){
            $str = (array)$str;
        }
        if(is_array($str) AND count($str)){
            $new_str = [];
            foreach($str as $key => $s){
                $array = self::objectToArray($s);
                $new_str[$key] = $array;
            }
            $str = $new_str;
        }
        self::echoAjax([$key_response => $str]);
    }

    public static function fajax($data)
    {
        if(self::$flag){
            self::ajax($data);
        }
    }

    public static function rn($data)
    {
        if(!DEBUG_RN){ return false; }
        return $data;
    }

    public static function clearEscapeU0000($str = ''){
        if($str == '') return $str;
        $str = preg_replace('/(\*)/i', '', $str);
        return trim($str);
    }

    public static function objectToArray($obj){
        $result = [];
        if(is_object($obj)){
            $array = (array)$obj;
            if(count($array)){
                foreach($array as $ar_key => $ar_val){
                    if(is_object($ar_val)){
                        $ar_val = self::objectToArray($ar_val);
                    }
                    $ar_key = self::clearEscapeU0000($ar_key);
                    $result[$ar_key] = $ar_val;
                }
            }
        }
        if(is_array($obj) OR is_string($obj) OR is_numeric($obj) OR is_bool($obj)){
            $result = $obj;
        }
        return $result;
    }

    public static function getThis($object){
        return get_class_vars(get_class($object));
    }

    public static function getPartStrByCharacter($url,$haracter,$code = false){

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
                if($pos != '') $str = substr($url, $pos + 1);
                else $str = $url;
                break;
            case 'string_all_from_first':
                $pos = strpos($url, $haracter);
                if($pos != '') $str = substr($url, $pos);
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

    /*
     * Получение расширения файла
     */
    public static function getExtension($file_name){
        // Разворачиваем строку наоборот
        $revstr = strrev($file_name);
        // Находим индекс первой точки
        $position = strpos($revstr, '.');
        // Отбрасываем имя файла
        $str_itog_rev = substr($revstr,0,$position);
        // Разворачиваем строку назад
        return strrev($str_itog_rev);
    }// function getExtension(...)

    /**
     * @param array $data
     * @return array
     */
    public static function serializeToArray($data = []){
        $array = [];
        if(count($data)){
            foreach($data as $d){
                if($d['value'] == 'on'){
                    $array[$d['name']] = true;
                }else{
                    $array[$d['name']] = $d['value'];
                }
            }
        }
        return $array;
    }

    public static function isLocal()
    {
        $local = false;
        if(!preg_match('~.kz~', $_SERVER['HTTP_HOST'])){
            $local = true;
        }
        return $local;
    }

    public static function forBy($data, $prop, $show = false)
    {
        $result = [];
        if(count($data) AND $prop){
            foreach($data as $item){
                if(is_object($data)){
                    $result[] = $item->$prop;
                }
                if(is_array($data)){
                    $result[] = $item[$prop];
                }
            }
        }
        if($show){
            if($show == 'pri') {
                self::pri($result);
            }
            if($show == 'pre') {
                self::pre($result);
            }
            return false;
        }
        return $result;
    }

    public static function post()
    {
        $post = Yii::$app->request->post();
//        self::ajax($post);
        if(isset($post['inputs'])){
            $post_inputs = self::serializeToArray($post['inputs']);
            $post = SArrayHelper::merge($post, $post_inputs);
        }
        return $post;
    }

    public static function date($timestamp)
    {
        $result = '';
        if(is_numeric($timestamp)){
            $result = date('Y-m-d H:i:s', $timestamp);
        }
        return $result;
    }

}// Class

