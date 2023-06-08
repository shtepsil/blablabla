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
 * Class NotResponding
 * Логика статуса Клиент не отвечает
 *
 * @package backend\components\ProcessOrder\Status
 */
class NotResponding extends OrderStatus
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
        if (!$this->order->canChangeStatus(9)) {
            throw new StateException('У вас нет доступа');
        }
        $this->order->status        = 9;
        $this->order->update_status = 9;
        return $this->order;
    }
}