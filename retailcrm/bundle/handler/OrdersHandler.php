<?php

class OrdersHandler implements HandlerInterface 
{
    public function prepare($data) {
        $this->container = Container::getInstance();
        $this->rule = new Rule();
        $orders = array();
        $statusMap = array(
            "0" => 'new',//Новый заказ
            "1" => 'assembling',//Заказ на формировании
            "2" => 'availability-confirmed',//Заказ на подтвержение клиентом
            "3" => 'client-confirmed',//Заказ подтверждён клиентом
            "4" => 'delivering',//Доставка
            "5" => 'complete',//Заказ оплачен/выполнен
            "6" => 'return',//Возврат
            "7" => 'part-return',//Частичный возврат
            "8" => 'cancel-other',//Отказ клиента
            "9" => 'no-call',//Клиент не отвечает
        );
        $paymentMap = array(
            "1" => 'cash',//Наличными
            "2" => 'bank-card', //Банковской карточкой
        );
        $managerMap = array(
            "19" => 10, // Золотарева Анна
            "20" => 9,  // Персидских Ольга
            "21" => 8,  // Коваль Галина
            "33" => 13, // Станислав Валюженич
            "32" => 14, // Миникеева Альфия
            "29" => 16, // Олеся Гайворонская
            "30" => 15, // Елена Филимонова
            "37" => 18, // Туктибаева Карлыгаш
            "38" => 20, // Резванова Алла
            "40" => 21, // Ислентьева Кристина
        );
        
        return $orders;
        
        foreach ($data as $record) {
            $order = array();
            $fio = DataHelper::explodeFIO(strip_tags($record['fio']));
            $order = array_merge($order, $fio);
            
            $order['externalId'] = $record['externalId'];
            $order['number'] = $record['externalId'];
            $order['createdAt'] = $record['createdAt'];
            $order['customerId'] = $record['customerId'];
            $order['phone'] = $record['phone'];
            $order['email'] = $record['email'];
            $order['customerComment'] = $record['customerComment'];
            $order['managerComment'] = $record['managerComment'];
            $order['discount'] = $record['discount'];
            $order['orderMethod'] = $record['orderMethod'];
            
            $order['delivery'] = array(
                'code' => 'self-delivery',
                'date' => $record['delivery_date'],
                'time' => array('custom' => $record['delivery_time']),
                'address' => array(
                    'text' => $record['delivery_text'],
                    'city' => $record['delivery_city']
                )
            );
            
            if (isset($paymentMap[$record['payment']])) {
                $order['paymentType'] = $paymentMap[$record['payment']];
            }
            
            if (isset($statusMap[$record['status']])) {
                $order['status'] = $statusMap[$record['status']];
            }
            
            if (isset($managerMap[$record['managerId']])) {
                $order['managerId'] = $managerMap[$record['managerId']];
            }
            
            $queryItems = $this->rule->getSQL('order_items');
            $this->sql = $this->container->db->prepare($queryItems);
            $this->sql->bindParam(':orderId', $order['externalId']);
            $this->sql->execute();
            $rawItems = $this->sql->fetchAll(PDO::FETCH_ASSOC);
            
            $queryHandling = $this->rule->getSQL('order_items_handling');
            $this->handling = $this->container->db->prepare($queryHandling);
            $this->handling->bindParam(':orderId', $order['externalId']);
            $this->handling->execute();
            $handlingItems = $this->handling->fetchAll(PDO::FETCH_ASSOC);
            
            $handling = array();
            if (!empty($handlingItems)) {
                foreach ($handlingItems as $record) {
                    $handling[$record['productId']][] = $record['handlingName'];
                }
            }
            
            foreach($rawItems as $rawItem) {
                $weight = floatval($rawItem['weight']);
                $count = floatval($rawItem['count']);
                $item = array(
                    'productId' => $rawItem['productId'],
                    //'initialPrice' => $rawItem['price']
                );
                
                if (!empty($handling[$rawItem['productId']])) {
                    foreach($handling[$rawItem['productId']] as $index => $handle) {
                        $item['properties'][] = array(
                            'code' => 'handling'.($index+1),
                            'name' => 'Способы разделки',
                            'value' => $handle
                        );
                    }
                }
                
                if ($rawItem['unit_value'] == $rawItem['unit']) { //'pc' && 'kg'
                    $item['quantity'] = $count;
                } elseif ($rawItem['unit_value'] == 'kg' && $rawItem['unit'] == 'pc') {
                    $item['quantity'] = $weight;
                    $item['properties'][] = array(
                        'code' => 'count',
                        'name' => 'Шт.',
                        'value' => $count
                    );
                } elseif ($rawItem['unit_value'] == 'pc' && $rawItem['unit'] == 'kg') {
                    $item['quantity'] = $count; // count = weight
                    $item['properties'][] = array(
                        'code' => 'actualweight',
                        'name' => 'Вес',
                        'value' => $weight
                    );
                }
                
                $order['items'][] = $item;
            }
            
            $orders[] = $order;
        }
        
        return $orders;
    }
}
