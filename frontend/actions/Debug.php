<?php

namespace frontend\actions;

use common\components\Debugger as d;
use common\models\Orders;
use common\models\User as ModelUser;

class Debug
{

    public $post = [];

    public function run()
    {
        $this->post = d::post();
//        d::ajax($this->post);
        $response = 'Debug->run()->switch:default';
        if(isset($this->post['type'])){
            switch ($this->post['type']) {
                case 'btn_push':
                    $response = $this->test();
                    break;
                case 'get_file_debug':
                    $response = $this->getFileDebug();
                    break;
                case 'clear_file_debug':
                    $response = $this->setFileDebug();
                    break;
                default:
                    $response = 'Debug->run()->switch:default';
            }
        }
        return $response;
    }

    /*
     * Кнопка "Нажать"
     */
    public function test()
    {
        $order = Orders::find()->orderBy(['id' => SORT_DESC])->one();
        return $order->pay_status;
    }

    public function getFileDebug()
    {
        $result = d::getDebug($this->post['file_debug_name']);
        return $result;
    }

    public function setFileDebug($data = '')
    {
        $result = d::clearDebug($this->post['file_debug_name']);
        return $result;
    }

} //Class
