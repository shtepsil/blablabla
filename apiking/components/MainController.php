<?php
/**
 *
 */
namespace apiking\components;

use frontend\assets\AppAsset;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Controller;

/**
 * Class MainController
 * @package frontend\components
 * @author lxShaDoWxl
 *
 * @property \common\models\User $user
 * @property \shadow\SSettings $settings
 * @property \frontend\components\FunctionComponent $function_system
 * @property \common\models\City $city_model
 * @property \frontend\assets\AppAsset $AppAsset
 */
class MainController extends Controller
{
    public $cart_count = 0;
    public $cart_items = [];
    public $cart_sets = [];
    public $user;
    public $AppAsset;
    public $settings;
    public $city = 1;
    public $city_model;
    public $function_system;
    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        parent::init();
        $this->settings = \Yii::$app->settings;
        $this->function_system = \Yii::$app->function_system;
        $this->cart_items = \Yii::$app->session->get('items', []);
        $this->cart_sets = \Yii::$app->session->get('sets', []);
        if(\Yii::$app->request->cookies->getValue('city_select')&&!\Yii::$app->session->get('city_select')){
            \Yii::$app->session->set('city_select', \Yii::$app->request->cookies->getValue('city_select'));
        }
        $this->city = \Yii::$app->session->get('city_select', 1);
        $citys = $this->function_system->getCity_all();
        if (!isset($citys[$this->city])) {
            $this->city = 1;
        }
        $this->city_model = $citys[$this->city];
        if ($this->cart_items) {
            $this->cart_count = count($this->cart_items);
        }
        if ($this->cart_sets) {
            $this->cart_count += count($this->cart_sets);
        }
        if (!\Yii::$app->user->isGuest) {
            $user = \Yii::$app->user->identity;
            if ($user) {
                $this->user = $user;
            } else {
                \Yii::$app->user->logout(false);
            }
        }
        $this->AppAsset = AppAsset::register($this->view);
    }

    public $breadcrumbs = [
        [
            'label' => 'Главная',
            'url' => ['site/index'],
        ]
    ];
    public function SeoSettings($type, $id, $title)
    {
        if ($type == 'module') {
            $this->active_module = $id;
        } elseif ($type == 'page') {
            $this->active_page = $id;
        }
        if ($type && $id) {
            $q = new Query();
            $table = 'seo';
            $q->distinct = true;
//            $q->select = 'IF(s_l.title<>"",  s_l.title,p.title) as title,
//							IF(s_l.keywords<>"",  s_l.keywords,p.keywords) as keywords,
//							IF(s_l.description<>"",  s_l.description,p.description) as description';
            $q->select([
                'title' => new Expression('IF(s_l.title<>"",  s_l.title,p.title)'),
                'keywords' => new Expression('IF(s_l.keywords<>"",  s_l.keywords,p.keywords)'),
                'description' => new Expression('IF(s_l.description<>"",  s_l.description,p.description)'),
            ]);
            $q->join('LEFT OUTER JOIN', 'seo_lang AS s_l', 's_l.owner_id=p.id AND s_l.lang_id=:lang');
//            $q->join = 'LEFT OUTER JOIN seo_lang AS s_l ON
//									s_l.owner_id=p.id AND s_l.lang_id=:lang';
            $q->andWhere('p.type=:type AND p.owner_id=:id');
//            $q->condition = 'p.type=:type AND p.owner_id=:id';
            $q->groupBy('p.id');
            $q->params = array(
                ':lang' => \Yii::$app->language,
                ':id' => $id,
                ':type' => $type
            );
//            $seo = Yii::app()->db->commandBuilder->createFindCommand($table, $q, 'p')->queryRow();
            $seo = $q->from(['p' => $table])->one();
            if ($seo && ($seo['description'] || $seo['keywords'] || $seo['title'])) {
                $this->view->title = $seo['title'] ? $seo['title'] : $title;
                $this->view->registerMetaTag([
                    'name' => 'description',
                    'content' => $seo['description'] ? $seo['description'] : $title
                ], 'description');
                $this->view->registerMetaTag([
                    'name' => 'keywords',
                    'content' => $seo['keywords'] ? $seo['keywords'] : $title
                ], 'keywords');
            } else {
                $this->view->title = $title;
                $this->view->registerMetaTag([
                    'name' => 'description',
                    'content' => $title
                ], 'description');
                $this->view->registerMetaTag([
                    'name' => 'keywords',
                    'content' => $title
                ], 'keywords');
            }
        } else {
            $this->view->title = $title;
            $this->view->registerMetaTag([
                'name' => 'description',
                'content' => $title
            ], 'description');
            $this->view->registerMetaTag([
                'name' => 'keywords',
                'content' => $title
            ], 'keywords');
        }
    }
    public $active_module;
    public $active_page;
    /**
     * @param $menu \backend\models\BaseMenu
     * @return string
     */
    public function activeMenu($menu)
    {
        /**
         * @var $module \backend\models\Module
         */
        $result = false;
        if ($menu->type) {
            switch ($menu->type) {
                case 'page':
                    $result = ($menu->owner_id == $this->active_page);
                    break;
                case 'module':
                    $result = ($menu->owner_id == $this->active_module);
                    break;
            }
        }
        return $result;
    }
}