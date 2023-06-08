<?php

namespace common\models\retailcrm;

use yii\base\Model;

class CrmOrder extends Model 
{
    protected $measure_data = [
        0 => 'kg',
        1 => 'pc'
    ];
    protected $measure_price_data = [
        0 => 'kg',
        1 => 'pc'
    ];
    
    public $externalId;
    public $number;
    public $orderMethod;
    public $firstName;
    public $lastName;
    public $patronymic;
    public $phone;
    public $email;
    public $customerComment;
    public $managerComment;
    public $discount;
    public $discountPercent;
    public $delivery;
    public $paymentType;
    public $status;
    public $managerId;
    public $items;
    
    protected function explodeFIO($string)
    {
        $result = array();
        $parse = (!$string) ? false : explode(" ", $string, 3);

        switch (count($parse)) {
            case 1:
                $result['firstName'] = $parse[0];
                $result['lastName'] = '';
                $result['patronymic'] = '';
                break;
            case 2:
                $result['firstName'] = $parse[0];
                $result['lastName'] = $parse[1];
                $result['patronymic'] = '';
                break;
            case 3:
                $result['firstName'] = $parse[0];
                $result['lastName'] = $parse[1];
                $result['patronymic'] = $parse[2];
                break;
            default:
                return false;
        }
        
        return $result;
    }
    
    protected function getCrmStatus($siteStatus) 
    {
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
        
        return (isset($statusMap[$siteStatus])) ? $statusMap[$siteStatus] : -1;
    }
    
    protected function getCrmPayment($sitePayment)
    {
        $paymentMap = array(
            "1" => 'cash',//Наличными
            "2" => 'bank-card', //Банковской карточкой
        );
        
        return isset($paymentMap[$sitePayment]) ? $paymentMap[$sitePayment] : -1;
        
    }
    
    protected function getCrmManager($siteManager)
    {
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
        
        return isset($managerMap[$siteManager]) ? $managerMap[$siteManager] : -1; 
    }
}
