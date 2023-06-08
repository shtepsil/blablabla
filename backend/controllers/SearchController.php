<?php

namespace backend\controllers;

use backend\AdminController;
use common\models\Items;
use common\models\Orders;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class SearchController extends AdminController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['loginAdminPanel'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'control' => ['post', 'get']
                ],
            ]
        ];
    }

    public function actionOrdersBuyers()
    {
        $result = [];
        $get = \Yii::$app->request->get();

        if (\Yii::$app->request->isAjax && (!empty($get['name']) || !empty($get['email']) || !empty($get['phone']))) {
            $query = Orders::find();

            if (!empty($get['email'])) {
                $string = $get['email'];
                $validator = new \yii\validators\RegularExpressionValidator(['pattern' => '/^[\-а-яё0-9a-z_\.@]{2,250}$/ui']);

                if ($validator->validate($string, $error)) {
                    $users = $query->select(['trim(user_mail)'])
                        ->where(['like', 'user_mail', $string])
                        ->groupBy(['trim(user_mail)'])
                        ->orderBy('trim(user_mail)')
                        ->asArray()
                        ->all();
                }
            }
            elseif (!empty($get['phone'])) {
                $string = $get['phone'];
                $validator = new \yii\validators\RegularExpressionValidator(['pattern' => '/^[0-9\-\+\(\)]{2,30}$/ui']);

                if ($validator->validate($string, $error)) {
                    $users = $query->select(['trim(user_phone)'])
                        ->where(['like', 'user_phone', $string])
                        ->groupBy(['trim(user_phone)'])
                        ->orderBy('trim(user_phone)')
                        ->asArray()
                        ->all();
                }
            }
            elseif (!empty($get['name'])) {
                $string = $get['name'];
                $validator = new \yii\validators\RegularExpressionValidator(['pattern' => '/^[\-а-яёa-z ]{2,250}$/ui']);

                if ($validator->validate($string, $error)) {
                    $users = $query->select(['trim(user_name)'])
                        ->where(['like', 'user_name', $string])
                        ->groupBy(['trim(user_name)'])
                        ->orderBy('trim(user_name)')
                        ->asArray()
                        ->all();
                }
            }

            if (!empty($users)) {
                $i = 0;
                foreach ($users as $user) {
                    $result[] = [
                        'id' => $i++,
                        'label' => current($user),
                        'value' => current($user)
                    ];
                }
            }
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $result;
    }

    public function actionGoods()
    {
        $result = [];
        $get = \Yii::$app->request->get();

        if (\Yii::$app->request->isAjax && !empty($get['text'])) {
            $string = $get['text'];
            $validator = new \yii\validators\RegularExpressionValidator(['pattern' => '/^[\-а-яё0-9a-z_]{2,250}$/ui']);

            if ($validator->validate($string, $error)) {
                $items = Items::find()->select(['name', 'article'])
                    ->where(['like', 'name', $string])
                    ->orWhere(['like', 'article', $string])
                    ->asArray()
                    ->all();
            }

            if (!empty($items)) {
                $i = 0;
                foreach ($items as $item) {
                    $result[] = [
                        'id' => $i++,
                        'label' => ($item['article'] ? $item['article'].' | ' : '').($item['name'] ? $item['name'] :  ''),
                        'value' => ($item['article'] ? $item['article'].' | ' : '').($item['name'] ? $item['name'] : '')
                    ];
                }
            }
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $result;
    }
}