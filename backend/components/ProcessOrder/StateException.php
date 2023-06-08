<?php
/**
 * Created by PhpStorm.
 * Project: kingfisher
 * User: lxShaDoWxl
 * Date: 22.04.18
 * Time: 17:24
 */

namespace backend\components\ProcessOrder;

use Throwable;
use yii\base\Exception;

class StateException extends Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message, 422);
    }
}