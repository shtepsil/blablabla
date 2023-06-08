<?php
namespace backend\controllers;

use backend\AdminController;
use backend\models\SUser;
use shadow\widgets\AdminActiveForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Response;

/**
 * Class SUsersController
 * @package backend\controllers
 * @property \backend\models\SUser $model
 */
class SUsersController extends AdminController
{
    public function init()
    {
        $this->model = new SUser();
        $action = 's-users';
        $this->url = [
            'back' => ['site/'.$action],
            'control' => [$action.'/control']
        ];
        $this->MenuActive('s-users');
        $this->breadcrumb[] = [
            'url' => ['site/'.$action],
            'label' => 'Сотрудники'
        ];
        $this->view->title = 'Сотрудники';
        $this->breadcrumb[] = [
            'url' => ['site/'.$action],
            'label' => $this->view->title
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
                        'actions'=>['control','save'],
                        'allow' => true,
                        'roles' => ['loginAdminPanel'],
                        'matchCallback'=>function(){
                            if(\Yii::$app->user->id==\Yii::$app->request->get('id')||\Yii::$app->user->id==\Yii::$app->request->post('id')){
                                return true;
                            }
                            return false;
                        }
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
    public function actionLogin($id)
    {
        $id_admin=\Yii::$app->user->id;
//        \Yii::$app->user->logout();
        /**
         * @var $user SUser
         */
        $user = SUser::findOne($id);
        if ($user) {
            \Yii::$app->user->login($user, 3600 * 24 * 30);
            \Yii::$app->session->set('return_admin', $id_admin);
        }
        return $this->goBack();
    }
    public function actionSave()
    {
        $record = $this->model;
        if ($id = Yii::$app->request->post('id')) {
            $record = $record->findOne($id);
        }
        if(\Yii::$app->user->can('admin')){
            $record->scenario = 'admin';
        }
        if ($record->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $record->on($record::EVENT_AFTER_VALIDATE, [$record, 'validateAll']);
                if ($errors = AdminActiveForm::validate($record)) {
                    $result['errors'] = $errors;
                } else {
                    $event = $record->isNewRecord ? $record::EVENT_BEFORE_INSERT : $record::EVENT_BEFORE_UPDATE;
                    $record->on($event, [$record, 'saveAll']);
                    $event_clear = $record->isNewRecord ? $record::EVENT_AFTER_INSERT : $record::EVENT_AFTER_UPDATE;
                    $record->on($event_clear, [$record, 'saveClear']);
                    $save = $record->save(false);
                    if ($save) {
                        if (Yii::$app->request->post('commit') == 1) {
                            $result['url'] = Url::to($this->url['back']);
                        } else {
                            $url = $this->url['control'];
                            $url['id'] = $record->id;
                            $result['url'] = Url::to($url);
                        }
                        $result['set_value']['id'] = $record->id;
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