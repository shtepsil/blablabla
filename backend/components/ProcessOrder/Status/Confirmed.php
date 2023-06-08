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

/**
 * Class Confirmed
 * Логика статуса подтверждён клиентом
 *
 * @package backend\components\ProcessOrder\Status
 */
class Confirmed extends OrderStatus
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
        if (!$this->order->canChangeStatus(3)) {
            throw new StateException('У вас нет доступа');
        }
        $this->order->status        = 3;
        $this->order->update_status = 3;
        if ($this->order->collector_id) {
            /**@var $collector SUser */
            $collector                        = SUser::findOne($this->order->collector_id);
            $this->order->send_message_status = [
                'user'   => Yii::$app->user->identity,
                'emails' => [
                    $collector->email
                ]
            ];
        } else {
            /**@var $collectors SUser[] */
            $collectors = SUser::findAll(['role' => 'collector']);
            if ($collectors) {
                $emails_collectors = [];
                foreach ($collectors as $collector) {
                    $emails_collectors[] = $collector->email;
                }
                if ($emails_collectors) {
                    $this->order->send_message_status = [
                        'user'   => Yii::$app->user->identity,
                        'emails' => $emails_collectors
                    ];
                }
            }
        }
        return $this->order;
    }
}