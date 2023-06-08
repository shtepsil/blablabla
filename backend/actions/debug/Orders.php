<?php

namespace backend\actions\debug;

use common\components\Debugger as d;
use common\models\Orders as ModelOrders;
use common\models\OrdersHistory;
use common\models\OrdersItems;
use common\models\OrdersItemsHanding;
use common\models\OrdersPay;
use common\models\OrdersRollbackItems;
use common\models\OrdersRollbackSets;
use common\models\OrdersSets;
use common\models\OrdersUnloading;
use common\models\User as ModelUser;

class Orders
{

    public $post = [];

    public function run()
    {
        $this->post = d::post();
        switch($this->post['type']){
            case 'get_order':
                $order = $this->getOrder();
                if($order){
                    return 'Указанный заказ существует';
                }else{
                    return 'Заказ не найден';
                }
                break;
            case 'delete_order':
                return $this->deleteOrder();
                break;
            default:
                d::ajax('User->run()->switch:default');
        }
    }

    public function getOrder()
    {
        return ModelOrders::findOne($this->post['order_id']);
    }

    public function deleteOrder()
    {
        $order = $this->getOrder();
        if($order){
            // Удаление истории заказов
            OrdersHistory::deleteAll([ 'order_id' => $order->id ]);
            // Удаление товаров из заказа
            $orders_items = OrdersItems::find()
                ->where([ 'order_id' => $order->id ])
                ->indexBy('id')->all();
            if(count($orders_items)){
                $orders_items_ids = array_keys($orders_items);
                foreach($orders_items as $orders_item){
                    $orders_item->delete();
                }
                OrdersItemsHanding::deleteAll([ 'orders_items_id' => $orders_items_ids ]);
            }
            OrdersPay::deleteAll([ 'order_id' => $order->id ]);
            OrdersRollbackItems::deleteAll([ 'order_id' => $order->id ]);
            OrdersRollbackSets::deleteAll([ 'order_id' => $order->id ]);
            OrdersSets::deleteAll([ 'order_id' => $order->id ]);
            OrdersUnloading::deleteAll([ 'order_id' => $order->id ]);
            if($order->delete()){
                return 'Заказ ' . $order->id . ' удалён';
            }
            else{
                return $order->getErrors();
            }
        }else{
            return 'Заказ не найден';
        }
    }

}//Class
