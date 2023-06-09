<?php
/**
 * Created by PhpStorm.
 * Project: kingfisher
 * User: lxShaDoWxl
 * Date: 03.12.15
 * Time: 11:26
 */
namespace backend\controllers;

use backend\AdminController;
use backend\models\SUserPlan;
use yii\bootstrap\Html;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Class SUserPlanController
 * @package backend\controllers
 * @property \backend\models\SUserPlan $model
 */
class SUserPlanController extends AdminController
{
    public function init()
    {
        $this->model = new SUserPlan();
        $controller_name = Inflector::camel2id($this->id);
        $this->url = [
            'back' => ["$controller_name/index"],
            'control' => ["$controller_name/control"]
        ];
        $this->view->title = 'Планы менеджеров';
        $this->MenuActive($controller_name);
        $this->breadcrumb[] = [
            'url' => ["$controller_name/index"],
            'label' => 'Планы менеджеров'
        ];
        parent::init(); // TODO: Change the autogenerated stub
    }
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
                        'roles' => ['admin'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'control' => ['post', 'get'],
//                    'filter' => ['post', 'get'],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $data['items'] = SUserPlan::find()->orderBy(['date_start' => SORT_DESC])->all();
        return $this->render('//modules/s_user_plans', $data);
    }
    public function actionAll($id)
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            /**@var $items SUserPlan[]*/
            $items = SUserPlan::find()
                ->andWhere(['<','date_start',time()])
                ->andWhere(
                [
                    'OR',
                    [
                        'user_id' => $id
                    ],
                    [
                        'user_id' => null
                    ],
                ]
            )->orderBy(['date_start' => SORT_DESC])->all();
            $result = '';
            foreach ($items as $item) {
                $result .= Html::tag('option', date('d/m/Y', $item->date_start) . ' - ' . date('d/m/Y', $item->date_end), ['value' => $item->id]);
            }
            return [
                'items' => $result
            ];
        }else{
            throw new BadRequestHttpException();
        }
    }
}