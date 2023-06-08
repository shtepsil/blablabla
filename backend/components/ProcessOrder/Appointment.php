<?php
namespace backend\components\ProcessOrder;

use common\components\Debugger as d;
use backend\components\ProcessOrder\Appointments\CollectorRole;
use backend\components\ProcessOrder\Appointments\DriverRole;
use backend\components\ProcessOrder\Appointments\ManagerRole;
use backend\models\SUser;
use common\models\Orders;
use frontend\components\SmsController;

class Appointment
{
    /**
     * @var Orders
     */
    private $order;
    public function __construct(Orders $order)
    {
        $this->order = $order;
    }
    /**
     * @param SUser $user
     *
     * @return Orders
     * @throws \backend\components\ProcessOrder\StateException
     */
    public function lock(SUser $user)
    {
        if(!$this->order->canLock()){
            throw new StateException('Данный заказ у другого пользователя');
        }
        if ($this->order->status == 0) {
            $role        = new ManagerRole($user, $this->order);
            // Для заказа устанавливаем ID пользователя админки
            $this->order = $role->lock();

			$phone = $this->order->user_phone;
			$order_id = $this->order->id;
			$full_price = $this->order->full_price;	
			$username = $user->username;
			$phone_manager = $user->phone;
			  
			SmsController::send_sms("$phone", "Заказ№ $order_id принят.Менеджер $username $phone_manager");

        } elseif ($this->order->status == 1) {
            $role        = new CollectorRole($user, $this->order);
            $this->order = $role->lock();
        } elseif ($this->order->status == 3) {
            $role        = new DriverRole($user, $this->order);
            $this->order = $role->lock();
        }
        return $this->order;
    }
 
    /**
     * @param SUser $user
     *
     * @return Orders
     * @throws \backend\components\ProcessOrder\StateException
     */
    public function unlock(SUser $user)
    {
        if(!$this->order->canUnLock()){
            throw new StateException('Данный заказ у другого пользователя');
        }
        $update = false;
        if ($this->order->status == 0) {
            $role        = new ManagerRole($user, $this->order);
            $this->order = $role->unlock();
            $update      = true;
        } elseif ($this->order->status == 1) {
            $role        = new CollectorRole($user, $this->order);
            $this->order = $role->unlock();
            $update      = true;
        } elseif ($this->order->status == 3) {
            $role        = new DriverRole($user, $this->order);
            $this->order = $role->unlock();
            $update      = true;
        }
        if ($update) {
            $this->order->changeHistory(11);
        }
        return $this->order;
    }

}