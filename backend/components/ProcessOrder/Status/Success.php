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
 * Class Success
 * Логика статуса оплачен/выполнен
 *
 * @package backend\components\ProcessOrder\Status
 */
class Success extends OrderStatus
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
        if (!$this->order->canChangeStatus(5)) {
			
		//	$t = $this->order->canChangeStatus(4);
            throw new StateException('У вас нет доступа');
        }
        $this->order->status        = 5;
        $this->order->update_status = 6;
        $this->order->commitBonus();
        $this->order->date_delivery = time();
        if ($this->order->manager_id != Yii::$app->user->id) {
            /**@var $collector SUser */
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