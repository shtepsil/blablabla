<?php
/**
 * Created by PhpStorm.
 * Project: morkovka
 * User: lxShaDoWxl
 * Date: 28.07.15
 * Time: 11:10
 */
namespace backend\controllers;

use backend\AdminController;
use common\models\Category;
use common\models\Items;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\View;
use shadow\plugins\seo\behaviors\SSeoBehavior;

/**
 * Class CategoryController
 * @package backend\controllers
 * @property \common\models\Category $model
 */
class CategoryController extends AdminController
{
    public function init()
    {
        $this->on(self::EVENT_BEFORE_ACTION, function () {
            $this->model = new Category();
        });
        $this->MenuActive('catalog');
        $this->view->title = 'Каталог';
        $this->breadcrumb[] = [
            'url' => ['category/index'],
            'label' => $this->view->title
        ];
        $this->url = [
            'back' => ['category/index'],
            'control' => ['category/control']
        ];
        parent::init(); // TODO: Change the autogenerated stub
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
         $result = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin','manager','collector','copywriter'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'control' => ['post', 'get'],
                    'filter' => ['post', 'get'],
                ],
            ],
        ];
		 if (SSeoBehavior::enableSeoEdit()) {
            $result['seo'] = [
                'class' => SSeoBehavior::className(),
                'nameTranslate' => 'name',
                'controller' => 'site',
                'action' => 'catalog',
                'parentRelation' => 'parent',
                'childrenRelation' => [
                    'categories',
                    'items',
                ],
            ];
        }


        return $result;
    }
    public function actionIndex()
    {
		$data['cats'] = (new Category())->array_lists();
		return $this->render('index', $data);
    }
    public function actionControl()
    {
        $this->view->title = 'Категория';
        $this->breadcrumb[] = [
            'url' => ['category/control'],
            'label' => $this->view->title
        ];
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
    public function actionFilter()
    {
        if (Yii::$app->request->post('filter')) {
            $params = $this->filter(Yii::$app->request->post('filter'));
            $criteria = Items::find();
            if (isset($params['limit'])) {
                $criteria->limit = $params['limit'];
            }
            if (isset($params['offset'])) {
                $criteria->offset = $params['offset'];
            }
            if (isset($params['search'])) {
                $search = $params['search'];
                if (is_array($search)) {
                    $query = ['OR'];
                    foreach ($search as $name => $val) {
                        if ($val=trim($val)) {
                            $query[] = ['like', '`items`.' . $name, $val];
                        }
                    }
                    if ($query != ['OR']) {
                        $criteria->andWhere($query);
                    }
                } else {
                    $criteria->orFilterWhere(['`items`.`name`' => $search]);
                }
            }
            if (isset($params['cat'])) {
                $criteria->distinct(true);
                $criteria->join('LEFT OUTER JOIN', '`items_category`', '`items_category`.`item_id`=`items`.`id`');
                $criteria->andWhere([
                    'OR',
                    [
                        '`items`.`cid`' => $params['cat'],
                    ],
                    [
                        '`items_category`.`category_id`' => $params['cat']
                    ]
                ]);
            }
        } else {
            $criteria = Items::find();
        }
        $criteria->andWhere(['`items`.isDeleted' => 0]);
        $count = $data['itemCount'] = $criteria->count();
        $pages = new Pagination(['totalCount' => $count]);
        $pages->setPageSize(20);
        if (isset($params['page'])) {
            $pages->setPage($params['page'], true);
        }
        $data['model'] = new Items();
        $data['columns'] = [
            'name' => [
                'name' => 'Название',
                'sorting' => 'asc',
                'class' => 'sorting'
            ],
            'article' => [
                'name' => 'Артикул',
                'sorting' => 'asc',
                'class' => 'sorting'
            ],
            'price' => [
                'name' => 'Цена',
                'sorting' => 'asc',
                'class' => 'sorting'
            ],
            'count' => [
                'name' => 'Кол-во',
                'sorting' => 'asc',
                'class' => 'sorting'
            ],
            'isVisible' => [
                'name' => 'Видимость',
                'sorting' => 'asc',
                'class' => 'sorting'
            ],
            'status' => [
                'name' => 'Наличие',
                'sorting' => 'asc',
                'class' => 'sorting'
            ],
            'googleFid' => [
                'name' => 'Google Fid',
                'sorting' => 'asc',
                'class' => 'sorting'
            ],
        ];
        if (isset($params['order'])) {
            $orders = [];
            foreach ($params['order'] as $key => $val) {
                $orders[$key] = (($val == 'asc') ? SORT_ASC : SORT_DESC);
                $data['columns'][$key]['sorting'] = (($val == 'asc') ? 'desc' : 'asc');
                $data['columns'][$key]['class'] = (($val == 'asc') ? 'sorting_desc' : 'sorting_asc');
            }
            $criteria->orderBy($orders);
        }
        $data['items'] = $criteria
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $data['pages'] = $pages;
        return $this->renderAjax('items', $data);
    }
    public function filter($filter)
    {
        $filter = trim($filter, '#');
        parse_str($filter, $params);
        return $params;
    }
    public function actionTransport()
    {
        if (Yii::$app->request->isAjax) {
            $result['url'] = Url::to(['category/index']);
            $main_cid = Yii::$app->request->post('main_cid');
            $to_cid = Yii::$app->request->post('to_cid');
            Yii::$app->db->createCommand()->update('items', ['cid' => $to_cid], ['cid' => $main_cid])->execute();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            return $this->render('transport');
        }
    }
}