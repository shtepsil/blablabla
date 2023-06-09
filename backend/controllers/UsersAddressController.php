<?php
namespace backend\controllers;

use backend\AdminController;
use common\models\UserAddress;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\Response;

/**
 * Class UsersAddressController
 * @package backend\controllers
 *
 * @property UserAddress $model
 *
 */
class UsersAddressController extends AdminController
{
    public function init()
    {
        $this->model = new UserAddress();
        $controller_name = Inflector::camel2id($this->id);
        $this->url = [
            'back' => ["$controller_name/index"],
            'control' => ["$controller_name/control"]
        ];
        $this->view->title = 'Адреса';
        $this->MenuActive($controller_name);
        $this->breadcrumb[] = [
            'url' => ["$controller_name/index"],
            'label' => 'Адреса'
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
                    ],
                    [
                        'actions' => ['deleted'],
                        'allow' => false,
                        'roles' => ['loginAdminPanel'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['manager'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'control' => ['post', 'get'],
                ],
            ],
        ];
    }
    public function actionControl()
    {
        $item = $this->model;
        if ($id = \Yii::$app->request->get('id')) {
            $item = $item->findOne($id);
        }
        $data['item'] = $item;
        if ($data['item']) {
            return $this->render('//control/form', $data);
        } else {
            return false;
        }
    }
    public function actionSave()
    {
        $record = $this->model;
        if ($id = Yii::$app->request->post('id')) {
            $record = $record->findOne($id);
        }
        if ($record->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
//				$record->on($record::EVENT_AFTER_VALIDATE, [$record, 'validateAll']);
                if ($errors = ActiveForm::validate($record)) {
                    $result['errors'] = $errors;
                } else {
//                    $event = $record->isNewRecord ? $record::EVENT_BEFORE_INSERT : $record::EVENT_BEFORE_UPDATE;
//                    $record->on($event, [$record, 'saveTemplate']);
//                    $event_clear = $record->isNewRecord ? $record::EVENT_AFTER_INSERT : $record::EVENT_AFTER_UPDATE;
//                    $record->on($event_clear, [$record, 'saveClear']);

                    $save = $record->save();
                    if ($save) {
                        $result['set_value']['id'] = $record->id;
                        if(Yii::$app->request->post('commit')==1){
                            $result['url'] = Url::to(['users/control', 'id'=>$record->user_id]);
                        }
                        $result['message']['success'] = 'Сохранено!';
                    } else {
                        $result['message']['error'] = 'Произошла ошибка!';
                    }
                }
                return $result;
            } else {
                $record->validate();
            }
        }
        if (!Yii::$app->request->isAjax) {
            return $this->goBack();
        }
    }
}