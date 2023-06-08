<?php

namespace common\components\api\onesignal;

use common\components\Debugger as d;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Client\Common\HttpMethodsClient as HttpClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use OneSignal\Config;
use OneSignal\OneSignal;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yii;

class ApiOneSignal
{

	/**
     * @param array $data
     * @return array
     */
    public static function send($data = [])
    {
        $result = [];
        $config = new Config();
        $params = Yii::$app->params['oneSignal'];
        $config->setApplicationId($params['app_id']);
        $config->setApplicationAuthKey($params['rest_api_key']);
        $config->setUserAuthKey($params['user_auth_key']);

        $guzzle = new GuzzleClient([ // http://docs.guzzlephp.org/en/stable/quickstart.html
            // ..config
        ]);

        $client = new HttpClient(new GuzzleAdapter($guzzle), new GuzzleMessageFactory());
        $api = new OneSignal($config, $client);

        // Основная настройка параметров
        $notification_params = [
            'headings' => [
                'en' => 'Notification header',
                'ru' => 'Заголовок'
            ],
            'contents' => [
                'en' => 'Notification message',
                'ru' => 'Текст уведомления'
            ],
        ];

        /**
         * Далее идёт настройка $notification_params
         * по параметрам $data
         */

        // Заголовок уведомления
        if(isset($data['header']) AND $data['header']){
            $notification_params['headings']['ru'] = $data['header'];
        }
        // Текст уведомления
        if(isset($data['message']) AND $data['message']){
            $notification_params['contents']['ru'] = $data['message'];
        }

        /*
         * IDs пользователей, кому отправить.
         * Если нет ни одного ID, то пуши отправляются
         * всем пользователям, зарегистрированным в приложении,
         * (т.е. всем, чьи ID есть в системе OneSignal)
         */
        if(isset($data['user_ids']) AND count($data['user_ids'])){
            $user_ids = [];
            foreach($data['user_ids'] as $user_id){
                $user_ids[] = (string)$user_id;
            }
            $notification_params['include_external_user_ids'] = $user_ids;
        }else{
            $notification_params['included_segments'] = ['All'];
        }

        // Шаблон для уведомлений. (Настраивается в ЛК OneSignal)
        if(isset($data['template_id']) AND $data['template_id']){
            $notification_params['template_id'] = $data['template_id'];
        }

        // Отправка произвольных данных
        if(
            isset($data['data']) AND is_array($data['data']) AND count($data['data']) >= 2
            AND array_key_exists('type', $data['data']) AND array_key_exists('id', $data['data'])
        ){
            $notification_params['data'] = $data['data'];
        }

        // Запрос в сервис OneSignal
        try{
            $api->notifications->add($notification_params);
            $result['message']['success'] = 'Уведомление отправлено';
        }catch(\Exception $e){
            $result['message']['error'] = 'Ошибка отправки уведомления';
        }

        return $result;

    }

}//Class