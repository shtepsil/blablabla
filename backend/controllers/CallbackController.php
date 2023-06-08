<?php
/**
 * Created by PhpStorm.
 * Project: morkovka
 * User: lxShaDoWxl
 * Date: 18.09.15
 * Time: 10:41
 */
namespace backend\controllers;

use backend\AdminController;
use common\models\Callback;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;

class CallbackController extends AdminController
{
    public function init()
    {
        $this->model = new Callback();
        $controller_name = Inflector::camel2id($this->id);

        $this->url = [
            'back' => ['site/'.$controller_name],
            'control' => [$controller_name.'/control']
        ];
        $this->view->title = 'Заказ звонка';
        $this->MenuActive($controller_name);
        $this->breadcrumb[] = [
            'url' => ['site/'.$controller_name],
            'label' => 'Заказ звонка'
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
                        'roles' => ['loginAdminPanel'],
                    ],
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
}