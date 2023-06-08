<?php

class CustomersUpdateHandler implements HandlerInterface
{
    public function prepare($data) {
        $customers = array();
        
        foreach ($data as $record) {
            $customer = array();
            $customer['externalId'] = $record['externalId'];
            $customer['customFields']['bonus'] = $record['bonus'];
            
            if (!empty($record['personalDiscount'])) {
                $customer['personalDiscount'] = doubleval($record['personalDiscount']);
            }
            
            $customers[] = $customer;
        }
        
        return $customers;
    }
}
