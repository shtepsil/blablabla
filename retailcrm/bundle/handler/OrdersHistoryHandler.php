<?php

class OrdersHistoryHandler implements HandlerInterface {

    public function __construct() {
        $this->container = Container::getInstance();
        $this->logger = new Logger();
        $this->rule = new Rule();
        $this->statusMap = array(
            'new' => "0", //Новый заказ
            'assembling' => "1", //Заказ на формировании
            'availability-confirmed' => "2", //Заказ на подтвержение клиентом
            'client-confirmed' => "3", //Заказ подтверждён клиентом
            'delivering' => "4", //Доставка
            'complete' => "5", //Заказ оплачен/выполнен
            'return' => "6", //Возврат
            'part-return' => "7", //Частичный возврат
            'cancel-other' => "8", //Отказ клиента
            'no-call' => "9", //Клиент не отвечает
        );
        $this->api = new RequestProxy(
            $this->container->settings['api']['url'],
            $this->container->settings['api']['key']
        );
    }

    public function prepare($data) {
        if (empty($data)) {
            return true;
        }
        
        foreach ($data as $record) {
            $this->logger->write(json_encode($record).PHP_EOL, $this->container->ordersHistoryInfoLog);
            if (isset($record['created'])) {
                $this->createOrder($record);
            } else {
//                if (!empty($record['items'])) {
//                    $this->updateOrderItems($record['items'], $record['externalId']);
//                }
//                
//                $this->updateOrder($record);
            }
        }
    }

    private function createOrder($record) {
        $paymentMap = array(
            'cash' => "1",//Наличными
            'bank-card' => "2", //Банковской карточкой
        );
        $managerMap = array(
            10 => "19", // Золотарева Анна
            9 => "20",  // Персидских Ольга
            8 => "21",  // Коваль Галина
            13 => "33", // Станислав Валюженич
            14 => "32", // Миникеева Альфия
            16 => "29", // Олеся Гайворонская
            15 => "30", // Елена Филимонова
            18 => "37", // Туктибаева Карлыгаш
            20 => "38", // Резванова Алла
            21 => "40", // Ислентьева Кристина
        );
        $createQuery = $this->rule->getSQL('orders_history_create');
        $this->sql = $this->container->db->prepare($createQuery);
        
        $customerId = isset($record['customer']['externalId']) ? $record['customer']['externalId'] : NULL;
        $this->sql->bindParam(':customerId', $customerId);
        $fio = implode(' ', array($record['firstName'], $record['lastName']));
        $this->sql->bindParam(':fio', $fio);
        $this->sql->bindParam(':phone', $record['phone']);
        $this->sql->bindParam(':email', $record['email']);
        $this->sql->bindParam(':address', $record['delivery']['address']['text']);
        $this->sql->bindParam(':customerComment', $record['customerComment']);
        $this->sql->bindParam(':summ', $record['summ']);
        $this->sql->bindParam(':purchSumm', $record['purchaseSumm']);
        $deliveryCost = isset($record['delivery']['cost']) ? $record['delivery']['cost'] : 0;
        $this->sql->bindParam(':deliveryCost', $deliveryCost);
        $paymentType = isset($paymentMap[$record['paymentType']]) ? $paymentMap[$record['paymentType']] : 0;
        $this->sql->bindParam(':paymentType', $paymentType);
        $bonusAdd = $record['summ']/100;
        $this->sql->bindParam(':bonusAdd', $bonusAdd);
        //$this->sql->bindParam(':bonusUse', "0");//$record['discount']
        //$this->sql->bindParam(':bonusManager', "0");
        //$this->sql->bindParam(':bonusDriver', "0");
        $cityId = 0;
        $city = isset($record['delivery']['address']['city']) ? trim($record['delivery']['address']['city']) : '';
        if (!empty($city)) {
            $cityQuery = 'SELECT id FROM `city` WHERE name = "'.$city.'"';
            $this->city = $this->container->db->prepare($cityQuery);
            $this->city->execute();
            $cityRecord = $this->city->fetch(PDO::FETCH_ASSOC);
            print_r($cityRecord);
            $cityId = !empty($cityRecord) ? $cityRecord['id'] : 0;
        }
        $this->sql->bindParam(':cityId', $cityId);
        $managerId = isset($managerMap[$record['managerId']]) ? $managerMap[$record['managerId']] : 0;
        $this->sql->bindParam(':managerId', $managerId);
        $deliveryDate = isset($record['delivery']['date']) ? strtotime($record['delivery']['date']) : time();
        $this->sql->bindParam(':dateDelivery', $deliveryDate);
        $this->sql->bindParam(':createdAt', strtotime($record['createdAt']));
        $this->sql->bindParam(':updatedAt', strtotime($record['createdAt']));
        $this->sql->bindParam(':managerComment', $record['managerComment']);

        try {
            $this->sql->execute();
            $oid = $this->container->db->lastInsertId();
            $this->logger->write(
                    'Order: ' . $oid. ' created'.PHP_EOL, $this->container->ordersHistoryInfoLog
            );
            if (empty($record['externalId'])) {
                $response = $this->api->ordersFixExternalIds(
                    array(
                        array(
                            'id' => (int) $record['id'],
                            'externalId' => $oid
                        )
                    )
                );
                // change number
                $this->api->ordersEdit(
                    array(
                        'id' => (int) $record['id'],
                        'number' => $oid.'C'
                    ),
                    'id'
                );
            } 
        } catch (PDOException $e) {
            $this->logger->write(
                    'PDO: ' . $e->getMessage(), $this->container->errorLog
            );
            return false;
        }
        
        if (!empty($record['items'])) {
            $this->createOrderItems($record['items'], $oid);
        }
        
        //orders history
//        $this->history = $this->container->db->prepare("INSERT INTO `orders_history`".
//                " (`order_id`, `user_id`, `user_name`, `action`, `created_at`, `updated_at`)".
//                " VALUES (".$oid.", '".$customerId."', '".$fio."', 1, ".strtotime($record['createdAt']).", ".
//                strtotime($record['createdAt']).")");
//        $this->history->execute();
    }

    private function createOrderItems($items, $orderId) {
        foreach($items as $item) {
            $orderOfferId = $item['offer']['externalId'];
            $price = $item['initialPrice'];
            $fixWeight = false;

            $idLen = strlen($orderOfferId);
            if ($idLen > 2 && '77' == substr($orderOfferId, 0, 2)) {
                $orderOfferId = substr($orderOfferId, 2);
                $fixWeight = true;
            }
            $offerQuery = $this->rule->getSQL('offers_uid');
            $this->offerQuery = $this->container->db->prepare($offerQuery);
            $this->offerQuery->bindParam(':itemId', $orderOfferId);
            $this->offerQuery->execute();
            $offer = $this->offerQuery->fetch(PDO::FETCH_ASSOC);

            if ($offer['unit_value'] == $offer['unit']) {
                $weight = 0;
                $count = $item['quantity'];
            } elseif ($offer['unit_value'] == 'kg' && $offer['unit'] == 'pc') {
                if ($fixWeight) {
                    $weight = $item['quantity']*$offer['weight'];
                    $count = $item['quantity'];
                    $price = $offer['price'];
                } else {
                    $weight = $item['quantity'];
                    $count = ($item['quantity'] / $offer['weight']);
                }
            } elseif ($offer['unit_value'] == 'pc' && $offer['unit'] == 'kg') {
                $weight = $item['quantity'];
                $count = $item['quantity'];
            }

            $itemsQuery = $this->rule->getSQL('orders_history_insert_items');
            $this->query = $this->container->db->prepare($itemsQuery);
            $this->query->bindParam(':orderId', $orderId);
            $this->query->bindParam(':productId', $orderOfferId);
            $this->query->bindParam(':price', $price);
            $this->query->bindParam(':quantity', floatval($count));
            $this->query->bindParam(':weight', floatval($weight));
            $this->query->bindParam(':purchasePrice', $offer['purchasePrice']);

            try {
                $this->query->execute();
            } catch (PDOException $e) {
                $this->logger->write(
                    'PDO: ' . $e->getMessage(),
                    $this->container->errorLog
                );
                return false;
            }
        }
    }

    private function updateOrder($record) {
        $statusMap = array(
            'new' => "0", //Новый заказ
            'assembling' => "1", //Заказ на формировании
            'availability-confirmed' => "2", //Заказ на подтвержение клиентом
            'client-confirmed' => "3", //Заказ подтверждён клиентом
            'delivering' => "4", //Доставка
            'complete' => "5", //Заказ оплачен/выполнен
            'return' => "6", //Возврат
            'part-return' => "7", //Частичный возврат
            'cancel-other' => "8", //Отказ клиента
            'no-call' => "9", //Клиент не отвечает
        );
        $paymentMap = array(
            'cash' => "1",//Наличными
            'bank-card' => "2", //Банковской карточкой
        );
        $managerMap = array(
            10 => "19", // Золотарева Анна
            9 => "20",  // Персидских Ольга
            8 => "21",  // Коваль Галина
            13 => "33", // Станислав Валюженич
            14 => "32", // Миникеева Альфия
            16 => "29", // Олеся Гайворонская
            15 => "30", // Елена Филимонова
            18 => "37", // Туктибаева Карлыгаш

        );
        
        $updateParams = array();
        $fio = array();
        $changedFio = false;
        
        if (isset($record['firstName'])) {
            $fio['firstName'] = $record['firstName'];
            $changedFio = true;
        }

        if (isset($record['lastName'])) {
            $fio['lastName'] = $record['lastName'];
            $changedFio = true;
        }
        
        if (isset($record['patronymic'])) {
            $fio['patronymic'] = $record['patronymic'];
            $changedFio = true;
        }
        
        if ($changedFio) {
            $res = $this->api->ordersGet($record['id'], 'id');
            $order = $res['order'];
            $newFio = array();
            $newFio[] = isset($fio['firstName']) ? $fio['firstName'] : $order['firstName'];
            $newFio[] = isset($fio['lastName']) ? $fio['lastName'] : $order['lastName'];
            $newFio[] = isset($fio['patronymic']) ? $fio['patronymic'] : $order['patronymic'];
            $updateParams[] = '`user_name` = "' . implode(' ', $newFio) . '"';
        }

        if (isset($record['email'])) {
            $updateParams[] = '`user_mail` = "' . $record['email'] . '"';
        }

        if (isset($record['phone'])) {
            $updateParams[] = '`user_phone` = "' . $record['phone'] . '"';
        }
        
        if (isset($record['delivery']['address'])) {
            $order = $this->api->ordersGet($record['externalId']);
            $updateParams[] = '`user_address` = "' . $order['order']['delivery']['address']['text'] . '"';
        }
        
        if (isset($record['delivery']['date'])) {
            $updateParams[] = '`date_delivery` = UNIX_TIMESTAMP("' . $record['delivery']['date'] . '")';
        }
        
        if (isset($record['delivery']['cost'])) {
            $updateParams[] = '`price_delivery` = ' . intval($record['delivery']['cost']);
        }
        
        if (!empty($record['paymentType'])) {
            $updateParams[] = '`payment` = "' . $paymentMap[$record['paymentType']] . '"';
        }

        if (!empty($record['status'])) {
            $updateParams[] = '`status` = "' . $statusMap[$record['status']]. '"';
        }
        
        if (!empty($record['summ'])) {
            $updateParams[] = '`full_price` = ' . $record['summ'];
        }
        
        if (!empty($record['purchaseSumm'])) {
            $updateParams[] = '`full_purch_price` = ' . $record['purchaseSumm'];
        }
        
        if (isset($record['discount'])) {
            $updateParams[] = '`bonus_use` = ' . $record['discount'];
            //TODO ? total = summ - discount
        }

        if (isset($record['discountPercent'])) {
            if (!isset($order)) {
                $order = $this->api->ordersGet($record['externalId']);
            }
            $discount = $order['order']['summ'] * $record['discountPercent'] / 100;
            $updateParams[] = '`bonus_use` = ' . sprintf("%01.2f", $discount);
            //TODO ? total = summ - discount
        }
        
        if (isset($record['managerComment'])) {
            $updateParams[] = '`admin_comments` = "' . $record['managerComment'] . '"';
        }

        if (isset($record['customerComment'])) {
            $updateParams[] = '`user_comments` = "' . $record['customerComment'] . '"';
        }
        
        if (isset($record['orderMethod'])) {
            switch ($record['orderMethod']) {
                case 'phone':
                    $updateParams[] = '`isPhoneOrder` = 1';
                    break;
                case 'one-click':
                    $updateParams[] = '`isFast` = 1';
                    break;
                case 'shopping-cart':
                    $updateParams[] = '`isPhoneOrder` = 0';
                    $updateParams[] = '`isFast` = 0';
                    break;
            }
        }
        
        if (isset($record['managerId'])) {
            $updateParams[] = '`manager_id` = "' . $managerMap[$record['managerId']] . '"';
        }

        if (!empty($updateParams)) {
            //$updateParams[] = '`updated_at` = "' . time() . '"';
            $updateParamsString = implode(', ', $updateParams);
            $update = 'UPDATE `orders` ' . 'SET ' . $updateParamsString . ' WHERE `id` = ' . $record['externalId'];
            $this->sql = $this->container->db->prepare($update);

        } else {
            return true;
        }

        try {
            $this->sql->execute();
        } catch (PDOException $e) {
            file_put_contents(__DIR__.'/exep.txt', var_export($e,true), FILE_APPEND);
            $this->logger->write(
                    'PDO: ' . $e->getMessage(), $this->container->errorLog
            );
            return false;
        }
    }

    private function updateOrderItems($items, $orderId) {
        if (empty($items) || empty($orderId)) {
            return false;
        }

        foreach ($items as $item) {
            if (isset($item['deleted'])) {
                $orderOfferId = $item['id'];
                $idLen = strlen($orderOfferId);
                if ($idLen > 2 && '77' == substr($orderOfferId, 0, 2)) {
                    $orderOfferId = substr($orderOfferId, 2);
                }
                $delQuery = $this->rule->getSQL('orders_history_delete_items');
                $this->delQuery = $this->container->db->prepare($delQuery);
                $this->delQuery->bindParam(':item_id', $orderOfferId);
                $this->delQuery->bindParam(':order_id', $orderId);
                try {
                    $this->delQuery->execute();
                    echo 'deleted';
                } catch (PDOException $e) {
                    $this->logger->write(
                            'PDO: ' . $e->getMessage(), $this->container->errorLog
                    );
                    return false;
                }
            } else {
                $orderOfferId = $item['offer']['externalId'];
                $price = $item['initialPrice'];
                $fixWeight = false;
                
                $idLen = strlen($orderOfferId);
                if ($idLen > 2 && '77' == substr($orderOfferId, 0, 2)) {
                    $orderOfferId = substr($orderOfferId, 2);
                    $fixWeight = true;
                }
                $offerQuery = $this->rule->getSQL('offers_uid');
                $this->offerQuery = $this->container->db->prepare($offerQuery);
                $this->offerQuery->bindParam(':itemId', $orderOfferId);
                $this->offerQuery->execute();
                $offer = $this->offerQuery->fetch(PDO::FETCH_ASSOC);
                
                if ($offer['unit_value'] == $offer['unit']) {
                    $weight = 0;
                    $count = $item['quantity'];
                } elseif ($offer['unit_value'] == 'kg' && $offer['unit'] == 'pc') {  
                    if ($fixWeight) {
                        $weight = $item['quantity']*$offer['weight'];
                        $count = $item['quantity'];
                        $price = $offer['price'];
                    } else {
                        $weight = $item['quantity'];
                        $count = ($item['quantity'] / $offer['weight']);
                    }
                } elseif ($offer['unit_value'] == 'pc' && $offer['unit'] == 'kg') {
                    $weight = $item['quantity'];
                    $count = $item['quantity'];
                }

                if (isset($item['created']) && !empty($item['created'])) {
                    $createQuery = $this->rule->getSQL('orders_history_create_items');
                    $this->query = $this->container->db->prepare($createQuery);
                    $this->query->bindParam(':order_id', $orderId);
                    $this->query->bindParam(':item_id', $orderOfferId);
                    $this->query->bindParam(':quantity', floatval($count));
                    $this->query->bindParam(':weight', floatval($weight));
                    $this->query->bindParam(':price', $price);
                    $this->query->bindParam(':purch_price', $offer['purchasePrice']);
                    // TODO bonus_manager

                    try {
                        $this->query->execute();
                        echo 'created';
                    } catch (PDOException $e) {
                        $this->logger->write(
                                'PDO: ' . $e->getMessage(), $this->container->errorLog
                        );
                        return false;
                    }
                } else {
                    $upQuery = $this->rule->getSQL('orders_history_update_items');
                    $this->upQuery = $this->container->db->prepare($upQuery);
                    $this->upQuery->bindParam(':order_id', $orderId);
                    $this->upQuery->bindParam(':item_id', $orderOfferId);
                    $this->upQuery->bindParam(':count', floatval($count));
                    $this->upQuery->bindParam(':weight', floatval($weight));
                    $this->upQuery->bindParam(':price', $price);

                    try {
                        $this->upQuery->execute();
                        echo 'updated';
                    } catch (PDOException $e) {
                        $this->logger->write(
                                'PDO: ' . $e->getMessage(), $this->container->errorLog
                        );
                        return false;
                    }
                }
            }
        }
    }
}
