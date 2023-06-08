<?php

class CustomersHandler implements HandlerInterface
{
    public function prepare($data) {
        $customers = array();
        $addressFields = array(
            "index",                      // Почтовый индекс
            "region",                     // Область
            "city",                       // Город
            "street",                     // Улица
            "building",                   // Номер дома
            "flat",                       // Номер квартиры или офиса
            "floor",                      // Этаж
            "house",                      // Строение/корпус
            "metro",                      // Метро
        );
        
        foreach ($data as $record) {
            $fio = DataHelper::explodeFIO($record['fio']);
            $customer = $fio;
            $customer['externalId'] = $record['externalId'];
            $customer['email'] = $record['email'];
            $customer['createdAt'] = $record['createdAt'];
            
            if (!empty($record['phone'])) {
                $customer['phones'][] = array('number' => $record['phone']);
            }
            
            foreach ($addressFields as $field) {
                if (isset($record['address_'.$field]) && !empty($record['address_'.$field])) {
                    $customer['address'][$field] = $record['address_'.$field];
                }
            }
            
            if (!empty($record['personalDiscount'])) {
                $customer['personalDiscount'] = doubleval($record['personalDiscount']);
            }
            
            if (!empty($record['bonus'])) {
                $customer['customFields']['bonus'] = $record['bonus'];
            }
            
            $customers[] = $customer;
        }
        
        return $customers;
    }
}
