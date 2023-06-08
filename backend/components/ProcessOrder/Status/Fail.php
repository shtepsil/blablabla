<?php
/**
 * Created by PhpStorm.
 * Project: kingfisher
 * User: lxShaDoWxl
 * Date: 22.04.18
 * Time: 19:54
 */

namespace backend\components\ProcessOrder\Status;

use backend\components\ProcessOrder\StateException;
use backend\models\SUser;
use Yii;
use yii\helpers\Html;

/**
 * Class Fail
 * Логика статуса Отказ клиента
 *
 * @package backend\components\ProcessOrder\Status
 */
class Fail extends OrderStatus
{
    /**
     * @return array|bool
     */
    public function validate()
    {
        return true;
    }
    /**
     * @return \common\models\Orders
     * @throws StateException
     */
    public function open()
    {
        if (!$this->order->canChangeStatus(8)) {
            throw new StateException('У вас нет доступа');
        }
        $this->order->status = 8;
        $this->order->update_status = 14;
        $this->order->rollbackBonus();
        return $this->order;
    }
}