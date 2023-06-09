<?php
namespace backend\controllers;

use backend\AdminController;
use backend\models\Menu;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;

/**
 * Class MenuController
 * @package backend\controllers
 * @property $model Menu
 */
class MenuController extends AdminController
{
    public function init()
    {
        $this->model = new Menu();
        $controller_name = Inflector::camel2id($this->id);
        $this->url = [
            'back' => ["$controller_name/index"],
            'control' => ["$controller_name/control"]
        ];
        $this->view->title = 'Меню';
        $this->MenuActive($controller_name);
        $this->breadcrumb[] = [
            'url' => ["$controller_name/index"],
            'label' => 'Меню'
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