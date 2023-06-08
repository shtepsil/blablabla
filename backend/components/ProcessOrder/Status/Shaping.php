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

/**
 * Class Shaping
 * Логика статуса сборки
 *
 * @package backend\components\ProcessOrder\Status
 */
class Shaping extends OrderStatus
{
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
        if (!$this->order->canChangeStatus(1)) {
            throw new StateException('У вас нет доступа');
        }
        $this->order->status        = 1;
        $this->order->update_status = 2;
        /**@var $collectors SUser[] */
        $collectors = SUser::findAll(['role' => 'collector']);
        if ($collectors) {
            $emails_collectors = [];
            foreach ($collectors as $collector) {
                $emails_collectors[] = $collector->email;
            }
            if ($emails_collectors) {
                $this->order->send_message_status = [
                    'user'   => \Yii::$app->user->identity,
                    'emails' => $emails_collectors
                ];
            }
        }
        return $this->order;
    }

    public function setStatusOrderToShaping()
    {
        $this->order->status        = 1;
        $this->order->update_status = 21;
        return $this->order;
    }
}