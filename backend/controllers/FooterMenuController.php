<?php
/**
 * Created by PhpStorm.
 * Project: morkovka
 * User: lxShaDoWxl
 * Date: 16.09.15
 * Time: 16:21
 */
namespace backend\controllers;

use backend\AdminController;
use backend\models\FooterMenu;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;

class FooterMenuController extends AdminController
{
    public function init()
    {
        $this->model = new FooterMenu();
        $controller_name = Inflector::camel2id($this->id);
        $this->url = [
            'back' => ["$controller_name/index"],
            'control' => ["$controller_name/control"]
        ];
        $this->view->title = 'Нижнее Меню';
        $this->MenuActive($controller_name);
        $this->breadcrumb[] = [
            'url' => ["$controller_name/index"],
            'label' => 'Нижнее Меню'
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
    public function actionIndex()
    {

        $data['params'] = call_user_func([$this->model->className(), 'getListItems']);
        return $this->render('//modules/menu',$data);
    }
}