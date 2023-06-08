<?php
/**
 * Created by PhpStorm.
 * Project: kingfisher
 * User: lxShaDoWxl
 * Date: 24.12.15
 * Time: 17:30
 */
namespace console\controllers;

use backend\models\SUser;
use common\models\Orders;
use yii\console\Controller;

/**
 * Class CronController
 * @package console\controllers
 */
class CronController extends Controller
{
    public function actionTestViktor()
    {
        $user = SUser::findOne(2);
        $orders = Orders::find()
            ->orderBy(['date_delivery' => 'DESC'])->limit(10)->all();
        \Yii::$app->mailer->compose(['html' => 'admin/info_delivery_order'], ['orders' => $orders, 'user' => $user])
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->params['siteName'] . ' info'])
            ->setTo($user->email)
            ->setSubject('Оповещение с сайта ' . \Yii::$app->params['siteName'] . '.kz')->send();
    }
    public function actionInfoDelivery()
    {
        /**
         * @var $user SUser
         */
        $time = time();
        $users = SUser::find()->andWhere(['role' => 'manager'])->all();
        $start_date = strtotime(date('d.m.Y', $time) . ' 00:00:00');
        $end_date = strtotime(date('d.m.Y', $time) . ' 23:59:59');
        foreach ($users as $user) {
            $orders = Orders::find()
                ->orderBy(['date_delivery' => SORT_DESC])
                ->andWhere(['manager_id' => $user->id])
                ->andWhere(
                    [
                        '>=',
                        'date_delivery',
                        $start_date
                    ]
                )->andWhere(
                    [
                        '<=',
                        'date_delivery',
                        $end_date
                    ]
                )->all();
            if ($orders) {
                \Yii::$app->mailer->compose(['html' => 'admin/info_delivery_order'], ['orders' => $orders, 'user' => $user])
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->params['siteName'] . ' info'])
                    ->setTo($user->email)
                    ->setSubject('Оповещение с сайта ' . \Yii::$app->params['siteName'] . '.kz')->send();
            }
        }
    }
}