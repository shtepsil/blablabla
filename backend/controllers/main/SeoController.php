<?php
/**
 * Created by PhpStorm.
 * Project: morkovka
 * User: lxShaDoWxl
 * Date: 21.09.15
 * Time: 10:56
 */
namespace backend\controllers\main;

use backend\AdminController;
use yii\data\Pagination;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * Class SeoController
 * @package backend\controllers\main
 * TODO надо сделать для мултиязычных
 */
class SeoController extends AdminController
{
    public $data_types = [
        'main' => [
            'title' => 'Основное',
            'table' => 'seo'
        ],
        'page' => [
            'title' => 'Страницы',
            'table' => 'pages',
            'label' => 'name'
        ],
        'module' => [
            'title' => 'Модули',
            'table' => 'module',
        ],
        'category' => [
            'title' => 'Категории',
            'table' => 'category',
        ],
        'item' => [
            'title' => 'Товары',
            'table' => 'items',
        ],
    ];
    private $main_types = [
        '1' => 'Главная'
    ];
    public $langs = [
        'ru' => 'ru',
    ];
    public $current_type = 'main';
    public function init()
    {
        $controller_name = Inflector::camel2id($this->id);
        $this->url = [
            'back' => [$controller_name . '/index'],
            'control' => [$controller_name . '/control']
        ];
        $this->view->title = 'SEO';
        $this->MenuActive($controller_name);
        $this->breadcrumb[] = [
            'url' => [$controller_name . '/index'],
            'label' => 'SEO'
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
    public function actionIndex($type = 'main')
    {
        $q = new Query();
        if (isset($this->data_types[$type])) {
            $this->current_type = $type;
        } else {
            $this->current_type = 'main';
            $type = 'main';
        }
        $table = ArrayHelper::getValue($this->data_types[$type], 'table', '');
        $label = ArrayHelper::getValue($this->data_types[$type], 'label', 'name');
        $selects = [
            's.id',
            's.title',
            's.type',
            's.keywords',
            's.description',
            's_l.id as id_lang',
            's_l.title as title_lang',
            's_l.description as description_lang',
            's_l.keywords as keywords_lang',
            's_l.lang_id as lang_id'
        ];
        if ($type == 'main') {
            $selects[] = 's.id as owner_id';
            $q->andWhere('s.type="main"');
            $q->from(['s' => 'seo']);
            $count = $q->count('s.id');
        } else {
            $q->join('LEFT OUTER JOIN', ['s' => 'seo'], 's.owner_id=p.id AND s.type=:type', [':type' => $type]);
            $selects[] = 'p.id as owner_id';
            $selects[] = 'p.' . $label . ' as `label`';
            $q->from(['p' => $table]);
            $count = $q->count('p.id');
        }
        $q->join('LEFT OUTER JOIN', ['s_l' => 'seo_lang'], 's_l.owner_id=s.id');
        $q->select($selects);
        $q->orderBy(['owner_id' => SORT_DESC]);
        $pages = new Pagination(['totalCount' => $count]);
        $pages->setPageSize(20);
        $seo = $q->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $items = [
            'main' => []
        ];
        $add_rows = $add_owner_id = [];
        foreach ($seo as $row) {
            if (!isset($row['label'])) {
                $label = $this->main_types[$row['owner_id']];
                $row['label'] = $label;
            }
            if ($row['id'] && !in_array($row['id'], $add_rows)) {
                $items['main'][] = $row;
                $add_rows[] = $row['id'];
                if (isset($row['owner_id'])) {
                    $add_owner_id[] = $row['owner_id'];
                }
            } elseif (!$row['id'] && !in_array($row['owner_id'], $add_owner_id)) {
                $items['main'][] = $row;
                $add_owner_id[] = $row['owner_id'];
            }
            if ($row['lang_id']) {
                $items[$row['lang_id']][$row['id']] = array(
                    'label' => $row['label'],
                    'id' => $row['id_lang'],
                    'owner_id' => $row['id'],
                    'title' => $row['title_lang'],
                    'keywords' => $row['keywords_lang'],
                    'description' => $row['description_lang']
                );
            }
        }
        return $this->render('index', ['items' => $items,'pages'=>$pages]);
    }
    public function actionSave($type = 'main')
    {
        $items = \Yii::$app->request->post('items');
        if (isset($this->data_types[$type])) {
            $old_items = (new Query())->indexBy('owner_id')->from('seo')->where(['type' => $type])->all();
            foreach ($items as $key => $value) {
                if ($key == 'main') {
                    foreach ($value as $id => $item) {
                        if (isset($old_items[$item['owner_id']])) {
                            $target = $old_items[$item['owner_id']];
                            if ($target['title'] != $item['title']
                                || $target['description'] != $item['description']
                                || $target['keywords'] != $item['keywords']
                            ) {
                                $data_update = [
                                    'title' => $item['title'],
                                    'description' => $item['description'],
                                    'keywords' => $item['keywords'],
                                ];
                                \Yii::$app->db->createCommand()->update('seo', ArrayHelper::htmlEncode($data_update), 'id=:id', [':id' => $target['id']])->execute();
                            }
                        } else {
                            if ($item['owner_id'] && ($item['title'] || $item['description'] || $item['keywords'])) {
                                $data_insert = [
                                    'type' => $type,
                                    'owner_id' => $item['owner_id'],
                                    'title' => $item['title'],
                                    'description' => $item['description'],
                                    'keywords' => $item['keywords'],
                                ];
                                \Yii::$app->db->createCommand()->insert('seo', $data_insert)->execute();
                            }
                        }
                    }
                }
            }
            \Yii::$app->session->setFlash('success', 'Сохранено!');
        } else {
            $type = 'main';
        }
        $params_url = ['seo/index', 'type' => $type];
        if (\Yii::$app->request->get('page')) {
            $params_url['page'] = \Yii::$app->request->get('page');
        }
        return $this->redirect($params_url);
    }
}