<?php

namespace frontend\models\retailcrm;

use common\models\retailcrm\CrmOrder;
use common\models\City;

class CreateCrmOrder extends CrmOrder 
{
    public $createdAt;
    public $customerId;   
    
    public function prepare($record) 
    {         
        $this->externalId = $record->id;
        $this->number = $record->id;
        $this->createdAt = date('Y-m-d H:i:s', $record->created_at);
        $this->customerId = $record->user_id;
        $fio = $this->explodeFIO($record->user_name);
        $this->firstName = $fio['firstName'];
        $this->lastName = $fio['lastName'];
        $this->patronymic = $fio['patronymic'];
        $this->phone = $record->user_phone;
        $this->email = $record->user_mail;
        $this->customerComment = $record->user_comments;
        $this->managerComment = $record->admin_comments;
        $this->discount = $record->bonus_use;
        if (!empty($record->discount)) {
            if (false != strstr($record->discount, '%')) {
                $this->discountPercent = intval($record->discount);                
            } else {
                $this->discount = $record->discount;
            }
        }
        $orderMethod = ($record->isFast > 0) ? 'one-click' : 'shopping-cart';
        if ($record->isPhoneOrder > 0) {
            $orderMethod = 'phone';
        }
        $this->orderMethod = $orderMethod;

        $this->delivery = array(
            'code' => 'self-delivery',
            'date' => date('Y-m-d', $record->date_delivery),
            'time' => array('custom' => $record->time_delivery),
            'cost' => intval($record->price_delivery),
            'address' => array(
                'text' => $record->user_address
            )
        );
        
        if ($record->city_id > 0) {
            $city = City::findOne($record->city_id);
            $this->delivery['address']['city'] = $city->name;
        }

        $payment = $this->getCrmPayment($record->payment);
        if ($payment != -1) {
            $this->paymentType = $payment;
        }
        
        $status = $this->getCrmStatus($record->status);
        if ($status != -1) {
            $this->status = $status;
        }

        $manager = $this->getCrmManager($record->manager_id);
        if ($manager != -1) {
            $this->managerId = $manager;
        }
        
        //items
        $orderItems = $record->getOrdersItems()->all();
        foreach($orderItems as $orderItemObj) {
            $orderItem = $orderItemObj->attributes;
            $catalogItem = $orderItemObj->item->attributes;
            
            $weight = floatval($orderItem['weight']);
            $count = floatval($orderItem['count']);
            $item = array(
                'productId' => $orderItem['item_id'],
                'initialPrice' => $orderItem['price']
            );
            
            $handling = $orderItemObj->ordersItemsHandings;
            if (!empty($handling)) {
                foreach($handling as $itemHandling) {
                    $handlingType = $itemHandling->typeHandling->attributes; 
                    $item['properties'][] = array(
                        'code' => 'handling'.$handlingType['id'],
                        'name' => 'Способы разделки',
                        'value' => $handlingType['name']
                    );
                }
            }
            
            $unit = $this->measure_data[$catalogItem['measure']];
            $unitPrice = $this->measure_price_data[$catalogItem['measure_price']];
            if ($unitPrice == $unit) { //'pc' && 'kg'
                $item['quantity'] = $count;
            } elseif ($unitPrice == 'kg' && $unit == 'pc') {
                $item['quantity'] = $weight;
                $item['properties'][] = array(
                    'code' => 'count',
                    'name' => 'Шт.',
                    'value' => $count
                );
            } elseif ($unitPrice == 'pc' && $unit == 'kg') {
                $item['quantity'] = $count; // count = weight
                $item['properties'][] = array(
                    'code' => 'actualweight',
                    'name' => 'Вес',
                    'value' => $weight
                );
            }
            
            $this->items[] = $item;
        }
    }
}
