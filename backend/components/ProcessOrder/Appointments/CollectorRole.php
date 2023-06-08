<?php
/**
 * Created by PhpStorm.
 * Project: kingfisher
 * User: lxShaDoWxl
 * Date: 22.04.18
 * Time: 17:12
 */

namespace backend\components\ProcessOrder\Appointments;

use backend\components\ProcessOrder\StateException;
use backend\models\SUser;
use common\models\Orders;

class CollectorRole implements AppointmentRole
{
    /**
     * @var SUser
     */
    private $user;
    /**
     * @var Orders
     */
    private $order;
    public function __construct(SUser $user, Orders $order)
    {
        $this->user  = $user;
        $this->order = $order;
    }
    /**
     * @return Orders
     */
    public function lock()
    {
        $this->order->collector_id    = $this->user->id;
        $this->order->update_status = 15;
        return $this->order;
    }
    /**
     * @return Orders
     * @throws StateException
     */
    public function unlock()
    {
        $this->order->collector_id = null;
        return $this->order;
    }
}