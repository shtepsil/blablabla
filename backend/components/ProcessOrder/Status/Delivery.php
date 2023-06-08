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
 * Class Delivery
 * Логика статуса Доставка
 *
 * @package backend\components\ProcessOrder\Status
 */
class Delivery extends OrderStatus
{
    /**
     * @return array|bool
     */
    public function validate()
    {
        if (!$this->order->driver_id) {
            return [
                'message' => [
                    'error' => 'Выберите водителя!'
                ],
                'errors'  => [
                    Html::getInputId($this->order, 'driver_id') => [
                        'Выберите водителя!'
                    ]
                ],
                'js'      => <<<JS
$('a[href="#page-responsible-panel"]').tab('show');
JS
            ];
        }
        return true;
    }
    /**
     * @return \common\models\Orders
     * @throws StateException
     */
    public function open()
    {
        if (!$this->order->canChangeStatus(4)) {
            throw new StateException('У вас нет доступа');
        }
        $this->order->status        = 4;
        $this->order->update_status = 5;
        if ($this->order->manager_id) {
            /**@var $collector SUser* */
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