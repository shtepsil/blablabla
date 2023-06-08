<?php
/**
 * Created by PhpStorm.
 * Project: kingfisher
 * User: lxShaDoWxl
 * Date: 22.04.18
 * Time: 19:58
 */

namespace backend\components\ProcessOrder\Status;

use backend\components\ProcessOrder\StateException;
use common\models\Orders;

abstract class OrderStatus
{
    protected $order;

    public function __construct(Orders $order, $options = [])
    {
        $this->order = $order;
        if ($options) {
            foreach ($options as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
        }
    }
    /**
     * @return mixed
     * @throws StateException
     */
    abstract public function open();
    /**
     * @return array|bool
     */
    abstract public function validate();
    public function setOptions($options)
    {
        foreach ($options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        return $this;
    }
}