<?php

namespace backend\components\ProcessOrder\Status;

class OrderStatusFactory
{
    /**
     * @param $order
     * @param $status
     * @param $options
     *
     * @return OrderStatus
     */
    public function createStatus($order, $status, $options = [])
    {
        switch ($status) {
            case 1:
                return new Shaping($order, $options);
                break;
            case 2:
                return new OnConfirmation($order, $options);
                break;
            case 3:
                return new Confirmed($order, $options);
                break;
            case 4:
                return new Delivery($order, $options);
                break;
            case 5:
                return new Success($order, $options);
                break;
            case 6:
                break;
            case 7:
                break;
            case 8:
                return new Fail($order, $options);
                break;
            case 9:
                return new NotResponding($order, $options);
                break;
            case 10:
                return new Recovery($order, $options);
                break;
        }
    }
}