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
 * Class OnConfirmation
 * Логика статуса на подтвержение клиентом
 *
 * @package backend\components\ProcessOrder\Status
 */
class OnConfirmation extends OrderStatus
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
        if (!$this->order->canChangeStatus(2)) {
            throw new StateException('У вас нет доступа');
        }
        $this->order->status        = 2;
        $this->order->update_status = 4;
        if ($this->order->manager_id) {
            /**@var $manager SUser */
            $manager                          = SUser::findOne($this->order->manager_id);
            $this->order->send_message_status = [
                'user'   => Yii::$app->user->identity,
                'emails' => [
                    $manager->email
                ]
            ];
        }
        return $this->order;
    }
}