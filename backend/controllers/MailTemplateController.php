<?php
/**
 * Created by PhpStorm.
 * Project: kingfisher
 * User: lxShaDoWxl
 * Date: 04.01.16
 * Time: 15:18
 */
namespace backend\controllers;

use backend\AdminController;
use backend\models\MailTemplate;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class MailTemplateController extends AdminController
{
    public function init()
    {
        $this->model = new MailTemplate();
        $this->url = [
            'back' => ['mail-template/control'],
            'control' => ['mail-template/control']
        ];
        $this->view->title = 'Текста писем';
        $this->MenuActive('mail-template');
        $this->breadcrumb[] = [
            'url' => ['mail-template/control'],
            'label' => 'Текста писем'
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