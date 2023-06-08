<?php

class SubscriberHandler implements HandlerInterface
{
    public function prepare($data)
    {
        $subscribers = array();
        
        foreach ($data as $record) {
            $subscriber = array();
            $subscriber['externalId'] = 'SUB'.$record['externalId'];
            list($firstName, $domen) = explode('@', $record['email']);
            $subscriber['firstName'] = $firstName;
            $subscriber['lastName'] = 'Подписчик '.$record['externalId'];
            $subscriber['email'] = $record['email'];
            $subscriber['createdAt'] = $record['createdAt'];
            
            $subscribers[] = $subscriber;
        }
        
        return $subscribers;
    }
}
