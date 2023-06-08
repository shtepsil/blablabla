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
 * Class Recovery
 * Логика статуса восстановление заказа после статуса not_responding и fail
 *
 * @package backend\components\ProcessOrder\Status
 */
class Recovery extends OrderStatus
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
        if (!$this->order->canChangeStatus(10)) {
            throw new StateException('У вас нет доступа');
        }
        $this->order->update_status = 13;
        if ($this->order->driver_id) {
            $this->order->status = 3;
        } else {
            $this->order->status = 0;
        }
        return $this->order;
    }
}