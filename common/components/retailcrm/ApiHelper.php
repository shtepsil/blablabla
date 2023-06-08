<?php

namespace common\components\retailcrm;

use RetailCrm\ApiClient;

class ApiHelper 
{
    private $api;
    private $url = 'https://kingfisher.retailcrm.ru';
    private $key = 'FSbYLrp8TyXPuC4fci1lVnLOj4NePZ9b';
    
    public function __construct() 
    {
        $this->api = new ApiClient($this->url, $this->key);
    }
    
    public function createCustomer($customer)
    {
        $this->api->customersCreate($customer);
    }
    
    public function updateOrder($order)
    {
        file_put_contents(__DIR__.'/updateOrderIn.txt', date("Y-m-d H-i-s"),FILE_APPEND);
        file_put_contents(__DIR__.'/updateOrderIn.txt', var_export($order,true),FILE_APPEND);
        $request = $this->api->ordersEdit($order);
        file_put_contents(__DIR__.'/updateOrderOut.txt', date("Y-m-d H-i-s"),FILE_APPEND);
        file_put_contents(__DIR__.'/updateOrderOut.txt', var_export($request,true),FILE_APPEND);
        return $request;
    }
    
    public function createOrder($order, $check = true)
    {
        if ($check) {
            $orders = $this->prepareOrders(array($order));
        }
        
        $chekedOrder = array_pop($orders);
        file_put_contents(__DIR__.'/chekedOrder.txt', date("Y-m-d H-i-s"),FILE_APPEND);
        file_put_contents(__DIR__.'/chekedOrder.txt', var_export($chekedOrder,true),FILE_APPEND);
        $request = $this->api->ordersCreate($chekedOrder);
        file_put_contents(__DIR__.'/requestCreate.txt', date("Y-m-d H-i-s"),FILE_APPEND);
        file_put_contents(__DIR__.'/requestCreate.txt', var_export($request,true),FILE_APPEND);
        return $request;
    }
    
    public function uploadOrders($orders, $check = true)
    {
        if ($check) {
            $orders = $this->prepareOrders($orders);
        }

        $splitOrders = array_chunk($orders, 50);

        foreach ($splitOrders as $orders) {
            $this->api->ordersUpload($orders);
            time_nanosleep(0, 250000000);
        }
    }
    
    private function prepareOrders($orders)
    {
        foreach ($orders as $idx => $order) {
            $customer = array();
            $customer['externalId'] = $order['customerId'];

            if (isset($order['firstName'])) {
                $customer['firstName'] = $order['firstName'];
            }

            if (isset($order['lastName'])) {
                $customer['lastName'] = $order['lastName'];
            }

            if (isset($order['patronymic'])) {
                $customer['patronymic'] = $order['patronymic'];
            }

            if (!empty($order['delivery']['address']['text'])) {
                $customer['address']['text'] = $order['delivery']['address']['text'];
            }
            
            if (!empty($order['delivery']['address']['city'])) {
                $customer['address']['city'] = $order['delivery']['address']['city'];
            }

            if (isset($order['phone'])) {
                $customer['phones'][]['number'] = $order['phone'];
            }

            if (isset($order['email'])) {
                $customer['email'] = $order['email'];
            }

            $checkResult = $this->checkCustomers($customer);

            if ($checkResult === false) {
                unset($orders[$idx]["customerId"]);
            } else {
                $orders[$idx]["customerId"] = $checkResult;
            }
        }

        return $orders;
    }

    private function checkCustomers($customer, $searchEdit = false)
    {
        $criteria = array(
            'name' => (isset($customer['phones'][0]['number'])) ? $customer['phones'][0]['number'] : $customer['lastName'],
            'email' => (isset($customer['email'])) ? $customer['email'] : ''
        );

        $search = $this->api->customersList($criteria);

        if (!is_null($search)) {
            if (empty($search['customers'])) {
                if (!is_null($this->api->customersEdit($customer))) {
                    return $customer["externalId"];
                } else {
                    return false;
                }
            } else {
                $_externalId = null;

                foreach ($search['customers'] as $_customer) {
                    if (!empty($_customer['externalId'])) {
                        $_externalId = $_customer['externalId'];
                        break;
                    }
                }

                if (is_null($_externalId)) {
                    $customerFix = array(
                        'id' => $search['customers'][0]['id'],
                        'externalId' => $customer['externalId']
                    );
                    $response = $this->api->customersFixExternalIds(
                        array($customerFix)
                    );
                    $_externalId = $customer['externalId'];
                };

                if ($searchEdit) {
                    $customer['externalId'] = $_externalId;
                    $this->api->customersEdit($customer);
                }

                return $_externalId;
            }
        } else {
            return false;
        }
    }
}
