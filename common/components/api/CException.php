<?php
/**
 * Created by PhpStorm.
 * User: Сергей
 * Date: 19.07.2021
 * Time: 11:20
 */

namespace common\components\api;

use common\components\Debugger as d;

class CException extends \Exception
{

    // Переопределим исключение так, что параметр message станет обязательным
//    public function __construct($message, $code = 0, \Exception $previous = null) {
    public function __construct($message = 'Ошибка Exception', $code = 0, \Exception $previous = null) {
        // некоторый код

        // убедитесь, что все передаваемые параметры верны
//        parent::__construct($message, $code, $previous);
        parent::__construct($message);
    }

    // Переопределим строковое представление объекта.
    public function __toString() {
//        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
        d::pe($this->message);
    }

}//Class