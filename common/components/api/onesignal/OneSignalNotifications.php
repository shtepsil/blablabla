<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 04.08.2022
 * Time: 16:20
 */

namespace common\components\api\onesignal;

use common\components\api\onesignal\ApiOneSignal;
use common\components\Debugger as d;
use common\models\Orders;
use common\models\OrdersItems;
use Yii;
use yii\web\Response;

class OneSignalNotifications
{
    /**
     * @param $type
     * @param array $data
     * @return array
     */
    public function start($type, $data = [])
    {
        $result = ['message' => ['error' => 'Неизвестный тип запроса']];
        switch($type){
            case 'bonus_add':
            // Уведомление о начислении бонусов за заказ
                // Если данные для отправки не пусты
                if(count($data)){
                    $result = $this->bonusAdd($data);
                }
                break;
            case 'action':
            // Уведомление для акций
                $result = $this->action();
                break;
        }
        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    public function send($data = [])
    {
        return ApiOneSignal::send($data);
    }

    /**
     * Ниже методы, которые распределены по задачам.
     * =============================================
     */

    /**
     * @param array $data
     * @return array
     */
    public function bonusAdd($data = [])
    {
        return $this->send($data);
    }

	/**
     * Отправка уведомлений для акций
     * @return array
     */
    public function action()
    {
        $post = Yii::$app->request->post();
        $send_result = [];
//        d::ajax($post);
        if(
            $post['Actions']['action_description'] != ''
            AND $post['Actions']['action_name'] != ''
        ) {
            $send_data = [
                'header' => $post['Actions']['action_name'],
                'message' => $post['Actions']['action_description'],
                'data' => [
                    'type' => 'PROMO',
                    'id' => $post['Actions']['action_id']
                ]
            ];
            if(isset($post['Actions']['send_all']) AND !$post['Actions']['send_all']){
                $orders = Orders::find()
                    ->select('user_id')
                    ->where(['id' => (
                    OrdersItems::find()
                        ->select('order_id')
                        ->where(['item_id' => $post['Actions']['items']])
                    )])
                    ->where(['not', ['pay_status' => null]])
                    ->andWhere(['isApp' => '1', 'status' => 3, 'pay_status' => 'success'])
                    ->indexBy('user_id')
                    ->asArray()->all();

                if ($orders) {
                    $user_ids = array_keys($orders);
                    $user_ids = [19299];
                    $send_data['user_ids'] = $user_ids;
                } else {
                    $send_result['message']['error'] = 'Ни одного заказа через приложение ещё не сделано';
                }
            }
            $send_result = $this->send($send_data);
        }else{
            $send_result['message']['error'] = 'Все поля акции должны быть заполнены';
        }

        return $send_result;
    }

}//Class