<?php
/**
 * Created by PhpStorm.
 * Project: morkovka
 * User: lxShaDoWxl
 * Date: 11.09.15
 * Time: 9:57
 */
namespace backend\controllers;

use backend\AdminController;
use common\models\Banners;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class BannersController extends AdminController
{
    public function init()
    {
        $this->model = new Banners();
        $this->url = [
            'back' => ['site/banners'],
            'control' => ['banners/control']
        ];
        $this->view->title = 'Баннеры';
        $this->MenuActive('banners');
        $this->breadcrumb[] = [
            'url' => ['site/banners'],
            'label' => 'Баннеры'
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