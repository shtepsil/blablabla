<?php

class CallbackHandler implements HandlerInterface
{
    public function prepare($data) {
        $callbacks = array();
        
        foreach ($data as $record) {
            $callback = array();
            $fio = DataHelper::explodeFIO(strip_tags($record['fio']));
            $callback = array_merge($callback, $fio);
            $callback['externalId'] = 'CB'.$record['externalId'];
            $callback['customerId'] = 0;
            $callback['number'] = 'CB'.$record['number'];
            $callback['createdAt'] = $record['createdAt'];
            $callback['phone'] = $record['phone'];
            $callback['status'] = $record['status'];
            $callback['orderMethod'] = 'callback';
            
            $callbacks[] = $callback;
        }
        
        return $callbacks;
    }
}
