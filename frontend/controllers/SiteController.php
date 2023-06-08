<?php

namespace frontend\controllers;


use backend\models\EditUser;
use common\components\Debugger as d;
use app\models\Auth;
use backend\models\Pages;
use common\models\AboutHistory;
use common\models\Actions;
use common\models\Banners;
use common\models\Category;
use common\models\City;
use common\models\Items;
use common\models\Jobs;
use common\models\News;
use common\models\Orders;
use common\models\Pickpoint;
use common\models\Recipes;
use common\models\Reviews;
use common\models\Sets;
use common\models\SpecActionCode;
use common\models\SpecActionPhone;
use common\models\User;
use common\models\Brands;
use frontend\components\MainController;
use frontend\components\MicroData;
use frontend\components\OtherDebugger;
use frontend\form\Order;
use frontend\form\PromoEnterCode;
use frontend\form\PromoRegistration;
use frontend\models\Delivery;
use frontend\widgets\ActiveForm;
use shadow\helpers\SArrayHelper;
use Yii;
use yii\bootstrap\Html;
use yii\caching\TagDependency;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Cookie;
use yii\web\Response;
use common\models\ReviewsItem;
use common\components\helpers\CHtml;
use frontend\components\SmsController;
use yii\widgets\LinkPager;

/**
 * Class SiteController
 * @package frontend\controllers
 * @property \frontend\assets\AppAsset $AppAsset
 */
class SiteController extends MainController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
//                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => '//site/error'
            ],
            'cart' => [
                'class' => 'frontend\components\CartAction',
            ],
            'tab-debug-ajax' => [
                'class' => 'frontend\actions\TabsAjaxActions',
                'actions' => [
                    'user' => 'User',
                    'debug' => 'Debug',
                    'jet-pay' => 'JetPay',
                ],
            ],
            'send-form' => [
                'class' => 'frontend\components\SendFormAction',
                'forms' => [
                    'registration' => 'Registration',
                    'registration_page' => 'RegistrationPage',
                    'login' => 'Login',
                    'recovery' => 'Recovery',
                    'user_info' => 'OrderUser',
                    'order' => 'Order',
                    'fast_order' => 'FastOrder',
                    'review_send' => 'ReviewSend',
                    'review_item' => 'ReviewItemSend',
                    'jobs_send' => 'JobsSend',
                    'callback' => 'CallbackSend',
                    'message' => 'MessageSend',
                    'subs' => 'Subscription',
                    'callback_set' => 'CallbackSetSend',
					'sms_login' => 'SmsLogin',
					'code_login' => 'CodeLogin'
                ]
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
//                'redirectView'=>'@frontend/views/redirect.php'
            ],
        ];
    }

    public function beforeAction($action)
    {
        /*
         * Если в шапке сайта был выбран какой то город,
         * то в GET будет параметр city.
         */
        if ($city_get = Yii::$app->request->get('city')) {
            // Получаем список городов
            $citys = $this->function_system->getData_city();
            // Если в списке городов есть выбранный город
            if (isset($citys[$city_get])) {
                // Устанавливаем сессию
                Yii::$app->session->set('city_select', $city_get);
                // Задаем настройки объекта куки
                $cookie = new Cookie([
                    'name' => 'city_select',
                    'value' => $city_get,
                    'expire' => time() + 604800,
                ]);
                // Записываем куку
                \Yii::$app->response->cookies->add($cookie);
//                return $this->redirect(['site/delivery', 'id' => $city_get]);
                /*
                 * Возвращаем пользователя обратно на страницу,
                 * на которой был выбран город.
                 * Проще говоря - просто перезагружаем текущую страницу.
                 */
                $http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;

                if($http_referer){
                    /*
                     * Если выбор города происходит на странице "Оплата и доставка"
                     * то редиректим пользователя
                     * на страницу описания доставки по выбранному городу
                     */
                    if(preg_match('/delivery/',$http_referer)){
                        return $this->redirect([
                            'site/delivery',
                            'id' => $city_get,
                        ]);
                    }else{
                        return $this->redirect($http_referer);
                    }
                }else{
                    return $this->redirect(['site/index']);
                }
            }else {
                return $this->redirect(['site/index']);
            }
        }

        return parent::beforeAction($action);

    }

    public function actionIndex()
    {
        $this->SeoSettings('main', 1, 'Главная');
        Yii::$app->opengraph->set([
            'image' => Yii::$app->request->getHostInfo() . '/assets/f5d2a9d/images/bg_kingfisher.jpg',
        ]);
        if ($code = Yii::$app->request->get('code')) {
            Yii::$app->session->set('invited_code', $code);
        }
        $items_hit = Items::find()
            ->where(['isVisible' => 1, 'isHit' => 1])
            ->limit(6)
            ->with('itemsCounts')
            ->all();
        $items_sale = Items::find()
            ->where(['isVisible' => 1])
            ->andWhere(['or', ['is not', 'old_price', null], ['is not', 'discount', null]])
            ->with('itemsCounts')
            ->all();
        $q_recipe = new ActiveQuery(Recipes::className());
        $q_recipe->andWhere(['isVisible' => 1]);
        $q_recipe->andWhere(['toMain' => 1]);
        $q_recipe->orderBy(['created_at' => SORT_DESC]);
        $q_recipe->limit(9);
        $recipes = $q_recipe->all();

        //   $banners = Banners::find()->orderBy(['sort' => SORT_ASC])->where(['isVisible' => 1])->all();

        $city_select = \Yii::$app->session->get('city_select', 1);

        $banners = Banners::find()
            ->join('LEFT JOIN', 'banners_cities', '`banners_cities`.`banner_id`= `banners`.id ')
            ->andWhere(
                [
                    'AND',
                    [
                        '`banners_cities`.`city_id`' => $city_select,
                    ],
                    [
                        '`banners`.isVisible' => 1,
                    ],
                ]
            )->all();

        $brands = Brands::find()->where(['isBanner' => 1])->all();

        $data = [
            'items_hit' => $items_hit,
            'items_sale' => $items_sale,
            'recipes' => $recipes,
            'banners' => $banners,
            'brands' => $brands,
        ];
        return $this->render('index', $data);
    }

    public function actionPage($id)
    {
        /**
         * @var $item Pages
         */
        $item = Pages::find()->andWhere(['isVisible' => 1, 'id' => intval($id)])->one();
        if ($item) {
            $this->SeoSettings('page', $item->id, $item->name);
            Yii::$app->opengraph->set([
                'title' => $item->name,
                'type' => 'Страница',
            ]);
            $this->breadcrumbs[] = [
                'label' => $item->name,
                'url' => ['site/page', 'id' => $item->id],
            ];
            $data['item'] = $item;
            return $this->render('page', $data);
        } else {
            throw new BadRequestHttpException();
        }
    }

    public function actionDelivery($id)
    {
        /**
         * @var $item City
         */
        $item = City::find()->andWhere(['id' => intval($id)])->one();
        if ($item) {
            $this->SeoSettings('city', $item->id, $item->name);
            $this->breadcrumbs[] = [
                'label' => $item->name,
                'url' => ['site/delivery', 'id' => $item->id],
            ];
            $data['item'] = $item;
            return $this->render('delivery_city', $data);
        } else {
            throw new BadRequestHttpException();
        }
    }

    public function actionContacts()
    {
        $this->SeoSettings('module', 1, 'Контакты');
        return $this->render('contact');
    }

    public function actionPaymentDelivery()
    {
        $item = Pages::findOne(5);
        $this->SeoSettings('module', 4, $item->name);
        return $this->render('payment_delivery', [
            'page' => $item,
        ]);
    }

    public function actionAbout()
    {
        $item = Pages::findOne(4);
        $this->SeoSettings('module', 5, $item->name);
        return $this->render('about', [
            'page' => $item,
            'history_all' => AboutHistory::find()->orderBy(['sort' => SORT_ASC])->all(),
        ]);
    }

    public function actionCatalog($id)
    {
        /**
         * @var $cat Category
         */

        $cat = Category::find()->where(['isVisible' => 1, 'id' => intval($id)])->one();
        if ($cat) {

            // Если пользователь не гость
            if (!Yii::$app->user->isGuest) {
                /*
                 * Если пользователь не оптовик
                 * и (категория/родитель категории только для оптовиков)
                 */
                if (
                    !Yii::$app->user->identity->isWholesale()
                    and (
                        ($cat->isWholesale and $cat->isWholesale > 0)
                        or ($cat->parent != null and $cat->parent->isWholesale > 0)
                    )
                ) {
                    // Не показываем категорию обычному пользователю
                    throw new BadRequestHttpException('Данная категория доступна только оптовикам');
                }
            }

            $this->SeoSettings('category', $cat->id, $cat->name);
            $this->breadcrumbs[] = [
                'label' => $cat->name,
                'url' => ['site/catalog', 'id' => $cat->id, 'slug' => $cat->slug],
            ];
            $data = [
                'cats' => [],
                'sub_cats' => [],
                'cat' => $cat,
                'sub_cat' => false
            ];

            $q = new ActiveQuery(Items::className());
            $all_parents = $cat->allParents();
            $count_parents = count($all_parents);
            if ($cat->type == 'cats' && $count_parents == 0) {
                $cats = $cat->getCategories()->where(['isVisible' => 1])->orderBy(['sort' => SORT_ASC])->indexBy('id')->all();
                $data['cats'] = $cats;
                $cats_a = array_keys($cats);
                $cats_a = Category::find()
                    ->orWhere(['parent_id' => $cats_a])
                    ->orWhere(['id' => $cats_a])
                    ->andWhere(['isVisible' => 1])
                    ->select(['id'])
                    ->column();
                $data['sub_cats'] = Category::find()
                    ->andWhere(['isVisible' => 1, 'type' => 'items', 'parent_id' => $cats_a])
                    ->all();
            } else {
                $cats_a = $cat->id;
                $cats[$cat->id] = $cat;
                if ($cat->parent_id && $count_parents != 2) {
                    $data['cats'] = Category::find()->where([
                        'isVisible' => 1,
                        'parent_id' => $cat->parent_id
                    ])->orderBy(['sort' => SORT_ASC])->indexBy('id')->all();
                } elseif ($count_parents == 2 && isset($all_parents[1])) {
                    $data['cats'] = Category::find()->where([
                        'isVisible' => 1,
                        'parent_id' => $all_parents[1]
                    ])->orderBy(['sort' => SORT_ASC])->indexBy('id')->all();
                    $data['cat'] = $cat->parent;
                    $data['sub_cat'] = $cat;
                }
                if ($count_parents == 1 && $cat->type == 'cats') {
                    $data['sub_cats'] = Category::find()
                        ->indexBy('id')
                        ->andWhere(['isVisible' => 1, 'type' => 'items', 'parent_id' => $cat->id])
                        ->all();
                    $cats_a = array_keys($data['sub_cats']);
                } elseif ($count_parents == 2) {
                    $data['sub_cats'] = Category::find()
                        ->indexBy('id')
                        ->andWhere(['isVisible' => 1, 'type' => 'items', 'parent_id' => $all_parents[0]])
                        ->all();
                    $cats_a = array_keys($cats);
                }
            }
            $q->join('LEFT OUTER JOIN', '`items_category`', '`items_category`.`item_id`=`items`.`id`');
            $q->andWhere([
                '`items`.isVisible' => 1,
            ]);
            $q->andWhere([
                'OR',
                ['`items`.cid' => $cats_a],
                ['`items_category`.category_id' => $cats_a]
            ]);
            $q->groupBy(['`items`.id']);
            // $count = $q->count();
            $order = Yii::$app->request->get('order', 'popularity_desc');
            switch ($order) {
                case 'price_asc':
                    $q->orderBy(['price' => SORT_ASC]);
                    $data['order'] = $order;
                    break;
                case 'price_desc':
                    $q->orderBy(['price' => SORT_DESC]);
                    $data['order'] = $order;
                    break;
                case 'name_asc':
                    $q->orderBy(['name' => SORT_ASC]);
                    $data['order'] = $order;
                    break;
                case 'name_desc':
                    $q->orderBy(['name' => SORT_DESC]);
                    $data['order'] = $order;
                    break;
                case 'popularity_desc':
                    $q->join('LEFT OUTER JOIN', '`orders_items`', '`orders_items`.`item_id`=`items`.`id`');
                    $q->join('LEFT OUTER JOIN', '`orders`', '`orders`.`id`=`orders_items`.`order_id`');
                    $q->select([
                        'items.*',
                        new Expression('count(orders.id) as popularity_count')
                    ]);
                    $q->orderBy(['popularity_count' => SORT_DESC]);
                    $data['order'] = $order;
                    break;
                default:
                    $q->join('LEFT OUTER JOIN', '`orders_items`', '`orders_items`.`item_id`=`items`.`id`');
                    $q->join('LEFT OUTER JOIN', '`orders`', '`orders`.`id`=`orders_items`.`order_id`');
                    $q->select([
                        'items.*',
                        new Expression('count(orders.id) as popularity_count')
                    ]);
                    $q->orderBy(['popularity_count' => SORT_DESC]);
                    $data['order'] = 'popularity_desc';

                    break;
            }
//            $pages = new Pagination(['totalCount' => $count]);
//            $pages->setPageSize(60);
//            if (isset($params['page'])) {
//                $pages->setPage($params['page'], true);
//            }
            $data['model'] = new Items();
            $items = $q->all();
            // $data['pages'] = $pages;
            $data['items'] = $items;
            //$this->redirect($item->url(), 301);

            return $this->render('catalog', $data);
        } else {
            throw new BadRequestHttpException('Данная категория не найдена');
        }
    }

    public function actionItem($id)
    {
        /**
         * @var $item Items
         */
        $item = Items::findOne(['id' => intval($id), 'isVisible' => 1]);
        //$this->checkSlug($item->slug, Yii::$app->request->get('slug'));

        if ($item) {
            //$this->redirect($item->url(), 301);
            $this->SeoSettings('item', $item->id, $item->name);
            Yii::$app->opengraph->set([
                'title' => $item->name,
                'image' => Yii::$app->request->getHostInfo() . $item->img_list,
                'type' => 'Каталог',
            ]);
            $cat = $item->c;
            if ($cat->parent_id) {
                if ($cat->parent->parent_id) {
                    $this->breadcrumbs[] = [
                        'label' => $cat->parent->parent->name,
                        'url' => $cat->parent->parent->url(),
                    ];
                }
                $this->breadcrumbs[] = [
                    'label' => $cat->parent->name,
                    'url' => $cat->parent->url(),
                ];
                $this->breadcrumbs[] = [
                    'label' => $cat->name,
                    'url' => $cat->url(),
                ];
            }

            $this->breadcrumbs[] = [
                'label' => $item->name,
                'url' => $item->url(),
            ];

            $data['item'] = $item;
            $data['recipes'] = Recipes::find()
                ->joinWith(['recipesItems'])
                ->andWhere(['recipes_item.item_id' => $item->id])
                ->limit(7)
                ->all();
            $data['associated'] = Items::find()
                ->select('items.*')
                ->join('INNER JOIN', 'item_associated', 'item_id_main=items.id OR item_id_sub=items.id')
                ->andWhere(['<>', 'items.id', $item->id])
                ->andWhere(['items.isVisible' => 1, 'item_associated.item_id_main' => $item->id])
                //                ->andWhere([
//                    'OR',
//                    ['item_associated.item_id_main' => $item->id],
//                    ['item_associated.item_id_sub' => $item->id]
//                ])
                ->limit(4)
                ->all();

            if (!empty($this->city) && (int) $this->city > 0) {
                $data['delivery'] = (new Delivery())->getDeliveryTextInItem((int) $this->city);
            } else {
                $data['deliveryFree'] = '';
            }

            /**
             * @var $reviews ReviewsItem[]
             */
            $reviews = ReviewsItem::find()
                ->where(['isVisible' => 1, 'item_id' => $item->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();

            $item->real_price();

            // Микроразметка
            $data['md'] = new MicroData($item, $reviews);

            $data['reviews'] = $reviews;

            // $this->redirect($item->url(), 301);
            return $this->render('item', $data);
        } else {
            throw new BadRequestHttpException('Данный товар не найден');
        }
    }

    public function actionRecipes()
    {
        $q = new ActiveQuery(Recipes::className());
        $q->andWhere(['isVisible' => 1]);
        $q->orderBy(['id' => SORT_DESC]);
        $count = $q->count();
        $pages = new Pagination(['totalCount' => $count]);
        $pages->setPageSize(19);
        $page = Yii::$app->request->get('page');
        if ($page && is_numeric($page)) {
            $pages->setPage($page, true);
        }
        $data['items'] = $q->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        /**
         * @var $pages Pagination
         */
        $pageCount = $pages->getPageCount();
        $hasPage = false;
        if (!($pageCount < 2)) {
            $currentPage = $pages->getPage();
            $pageCount = $pages->getPageCount();
            if (!($currentPage >= $pageCount - 1)) {
                $hasPage = true;
            }
        }
        $data['hasPage'] = $hasPage;
        $data['pages'] = $pages;
        $items = $q->all();
        $data['items'] = $items;
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $content_items = $this->renderPartial('//blocks/recipe', ['items' => $items]);
            $data['items'] = $content_items;
            return $data;
        } else {
            $this->SeoSettings('module', 2, 'Рецепты');
            $this->breadcrumbs[] = [
                'label' => 'Рецепты',
                'url' => ['site/recipes'],
            ];
            return $this->render('recipes', $data);
        }
    }

    public function actionRecipe($id)
    {
        /**
         * @var $item Recipes
         */
        $item = Recipes::findOne(['id' => intval($id), 'isVisible' => 1]);
        if ($item) {
            $seo = $this->SeoSettings('recipe', $item->id, $item->name);
            Yii::$app->opengraph->set([
                'title' => $item->name,
                'image' => Yii::$app->request->getHostInfo() . $item->img_list,
                'type' => 'Рецепты',
            ]);
            $this->breadcrumbs[] = [
                'label' => 'Рецепты',
                'url' => ['site/recipes'],
            ];
            $this->breadcrumbs[] = [
                'label' => $item->name,
                'url' => ['site/recipe', 'id' => $item->id],
            ];
            $data['item'] = $item;

            if ($seo)
                $data['seo'] = $seo;
            else {
                $data['seo'] = [
                    'description' => '',
                    'keywords' => '',
                    'title' => '',
                ];
            }
            return $this->render('recipe', $data);
        } else {
            throw new BadRequestHttpException('Данный рецепт не найдена');
        }
    }

    public function actionActions()
    {
        $this->breadcrumbs[] = [
            'label' => 'Акции',
            'url' => ['site/actions'],
        ];

        $actions_obj = Actions::find()->where(['isVisible' => 1]);

        $user = $this->user;
        if ($user) {
            if ($user->isWholesale()) {
                $actions_obj->andWhere(['>', 'isWholesale', '0']);
            } else {
                $actions_obj->andWhere(['isWholesale' => '0']);
            }
        }
        if ($id = Yii::$app->request->get('id')) {
            /**
             * @var $item News
             */
            if ($item = $actions_obj->andWhere(['id' => intval($id)])->one()) {
                $this->SeoSettings('actions', $item->id, $item->name);
                Yii::$app->opengraph->set([
                    'title' => $item->name,
                    'image' => Yii::$app->request->getHostInfo() . $item->img,
                    'type' => 'Акции',
                ]);
                $this->breadcrumbs[] = [
                    'label' => $item->name,
                    'url' => ['site/actions', 'id' => $item->id],
                ];
                $actions = Actions::find()
                    ->andWhere(['<>', 'id', $item->id])
                    ->andWhere(['isVisible' => 1])
                    //                    ->andWhere(['>=', 'created_at', $item->created_at - 2629743])
//                    ->andWhere(['<=', 'created_at', $item->created_at + 2629743])
                    ->andWhere(['>=', 'date_end', time()])
                    ->limit(4)
                    ->all();
                return $this->render('actions_one', ['item' => $item, 'actions' => $actions]);
            } else {
                throw new BadRequestHttpException('Запрошенная акция не найдена');
            }
        } else {
            $this->SeoSettings('module', 8, 'Акции');
            $data = [];
            $q = $actions_obj->orderBy(['date_end' => SORT_DESC]);
            $count = $q->count();
            $pages = new Pagination(['totalCount' => $count]);
            $pages->setPageSize(8);
            //            d::pex($q->createCommand()->sql);
            $items = $q->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
            //            d::pex($items);
            if ($items) {
                $data['items'] = $items;
            } else {
                throw new BadRequestHttpException('Не найдено ни одной акции');
            }
            $data['pages'] = $pages;

            return $this->render('actions', $data);
        }
    }

    public function actionBrands($category_id = null)
    {
        $this->breadcrumbs[] = [
            'label' => 'Бренды',
            'url' => ['site/brands'],
        ];
        if ($id = Yii::$app->request->get('id')) {
            /**
             * @var $brand Brands
             */
            if ($brand = Brands::find()->andWhere(['id' => intval($id), 'isVisible' => 1])->one()) {
                $this->SeoSettings('brands', $brand->id, $brand->name);

                $cat = null;
                if ($category_id) {
                    $cat = Category::findOne((int) $category_id);
                    if (!$cat) {
                        throw new BadRequestHttpException();
                    }
                }

                $url_params = [];

                $q = new ActiveQuery(Items::className());
                $q->andWhere(
                    [
                        '`items`.isVisible' => 1,
                        '`items`.isDeleted' => 0,
                    ]
                );
                $q_filter = new Query();
                $q_filter->orderBy(['`options_category`.`sort`' => SORT_ASC]);
                $q_filter->groupBy(
                    [
                        '`item_options_value`.`id`',
                    ]
                );
                $q_filter->from(['options']);
                $q_filter->join('LEFT JOIN', 'item_options_value', '`item_options_value`.`option_id` = `options`.`id`');
                $q_filter->join('LEFT JOIN', 'items', '`items`.id = `item_options_value`.item_id');
                $q_filter->join('LEFT JOIN', 'options_value', '`options_value`.id = `item_options_value`.option_value_id');
                if (Yii::$app->function_system->enable_multi_lang()) {
                    //region Для мультиязычности
                    $q_filter->join(
                        'LEFT JOIN',
                        'options_value_lang',
                        '`options_value_lang`.`owner_id`= `options_value`.id AND `options_value_lang`.language=:language'
                    );
                    $q_filter->join(
                        'LEFT JOIN',
                        'options_lang',
                        '`options_lang`.`owner_id`= `options`.id AND `options_lang`.language=:language'
                    );
                    $q_filter->addParams([':language' => Yii::$app->language]);
                    //endregion
                    $q_filter->select(
                        [
                            '`options`.`id`',
                            '`options`.`name`',
                            '`options`.`type`',
                            '`options`.`measure`',
                            '`l_name`' => '`options_lang`.`name`',
                            '`options_value`.`option_id`',
                            '`options_value`.`value`',
                            '`l_value`' => '`options_value_lang`.`value`',
                            '`value_id`' => '`options_value`.`id`',
                            'item_option_value_min' => 'item_options_value.`value`',
                            'item_option_value_max' => 'item_options_value.`max_value`',
                        ]
                    );
                } else {
                    $q_filter->select(
                        [
                            '`options`.`id`',
                            '`options`.`name`',
                            '`options`.`type`',
                            '`options`.`measure`',
                            '`l_name`' => new yii\db\Expression('NULL'),
                            '`options_value`.`option_id`',
                            '`options_value`.`value`',
                            '`l_value`' => new yii\db\Expression('NULL'),
                            '`value_id`' => '`options_value`.`id`',
                            'item_option_value_min' => 'item_options_value.`value`',
                            'item_option_value_max' => 'item_options_value.`max_value`',
                        ]
                    );
                }
                $q_filter->andWhere(['`items`.`isVisible`' => 1]);
                $params_request = [];
                if ($filter_params = \Yii::$app->request->post('filter', \Yii::$app->request->get('filter'))) {
                    $params_request = Items::parseCode($filter_params);
                }


                $cats = Category::find()
                    ->select(['category.*', 'countItems' => new Expression('COUNT(`items`.id)')])
                    ->distinct(true)
                    ->where(['category.isVisible' => 1])
                    ->orderBy(['category.sort' => SORT_ASC])
                    ->join('LEFT JOIN', 'items_category', '`items_category`.`category_id` = `category`.`id`')
                    ->join(
                        'INNER JOIN',
                        'items',
                        [
                            'AND',
                            [
                                'OR',
                                '`items`.`id`=`items_category`.`item_id`',
                                '`items`.`cid`=`category`.`id`',
                            ],
                            [
                                '`items`.`brand_id`' => $brand->id,
                            ],
                        ]
                    )
                    ->all();

                $data_cats = Category::find()->andWhere(['type' => 'items'])->orderBy(
                    ['sort' => SORT_ASC]
                )->all();

                foreach ($data_cats as $result) {
                    $data_cats_array[$result->id] = Items::find()
                        ->andWhere(
                            [
                                'AND',
                                [
                                    '`items`.cid' => $result->id,
                                ],
                                [
                                    '`items`.brand_id' => $brand->id,
                                ],
                                [
                                    '`items`.isVisible' => 1,
                                ],
                            ]
                        )->all();
                }
                $cats_a = [];

                if ($cat) {
                    if ($cat->type === 'items') {
                        $cats_a[] = $cat->id;
                    } else {
                        $cats_a = $cat->getAllSubItemCats();
                    }
                    $q->join('LEFT JOIN', 'items_category', '`items_category`.`item_id` = `items`.`id`')
                        ->andWhere(
                            [
                                'OR',
                                ['`items_category`.category_id' => $cats_a],
                                ['`items`.cid' => $cats_a],
                            ]
                        );
                } else {
                    foreach ($cats as $brandCat) {
                        if ($brandCat->type === 'items') {
                            $cats_a[] = $brandCat->id;
                        } else {
                            $cats_a = ArrayHelper::merge($cats_a, $brandCat->getAllSubItemCats());
                        }
                    }
                }

                $enable_filter = false;
                $data = [
                    'enable_filter' => $enable_filter,
                    'cats' => $cats,
                    'cat' => $cat,
                    'brand' => $brand,
                    'max_price' => 0,
                    'min_price' => 0,
                    'sel_brands' => [],
                    'sel_filter' => [],
                    'data_cats' => $data_cats,
                    'data_cats_array' => $data_cats_array
                ];
                $q->andWhere(['`items`.brand_id' => $brand->id]);

                $q->distinct(true);
                if ($enable_filter) {
                    //region сделано для того если есть чекбокс(использовать как фильтр) в характеристиках категории
                    $q_filter->join(
                        'LEFT JOIN',
                        'options_category',
                        '`options_category`.`option_id`= `options`.id AND `options_category`.`cid`=:cat_filter'
                    );
                    $q_filter->join(
                        'LEFT JOIN',
                        'options_category',
                        [
                            '`options_category`.`option_id`= `options`.id',
                            '`options_category`.`cid`' => $cats_a,
                        ]
                    );
                    //endregion
                    $q_filter->andWhere(
                        [
                            '`items`.isDeleted' => 0,
                            '`items`.isVisible' => 1,
                        ]
                    );
                    $q_filter->join('LEFT JOIN', 'items_category', '`items_category`.`item_id` = `items`.`id`')
                        ->andWhere(
                            [
                                'OR',
                                ['`items_category`.category_id' => $cats_a],
                                ['`items`.cid' => $cats_a],
                            ]
                        );
                    // $filters = Yii::$app->cache->get(
                    // [
                    // 'brand_filters',
                    // $brand->id,
                    // ]
                    // );
                    $filters = false;
                    if ($filters === false) {
                        $filters = [];
                        $filters_all = $q_filter->all();
                        foreach ($filters_all as $key => $value) {
                            if (!isset($filters[$value['id']])) {
                                $filters[$value['id']]['name'] = ($value['l_name']) ? $value['l_name'] : $value['name'];
                                $filters[$value['id']]['type'] = $value['type'];
                                $filters[$value['id']]['option_id'] = $value['id'];
                                $filters[$value['id']]['values'] = [];
                            }
                            if ($value['type'] == 'multi_select' || $value['type'] == 'one_select') {
                                $value_option = trim($value['l_value']);
                                if (!$value_option) {
                                    $value_option = $value['value'];
                                }
                                if (!in_array($value_option, $filters[$value['id']]['values'])) {
                                    $filters[$value['id']]['values'][$value['value_id']] = $value_option;
                                }
                            } else {
                                if ($value['type'] == 'range') {
                                    $min_value = floatval(preg_replace('/[^0-9.,]*/', '', $value['item_option_value_min']));
                                    $max_value = floatval(preg_replace('/[^0-9.,]*/', '', $value['item_option_value_max']));
                                    if (!isset($filters[$value['id']]['values']['min'])) {
                                        $filters[$value['id']]['values']['min'] = $min_value;
                                    } elseif ($min_value < $filters[$value['id']]['values']['min']) {
                                        $filters[$value['id']]['values']['min'] = $min_value;
                                    }
                                    if ($max_value < $filters[$value['id']]['values']['min']) {
                                        $filters[$value['id']]['values']['min'] = $max_value;
                                    }
                                    if (!isset($filters[$value['id']]['values']['max'])) {
                                        $filters[$value['id']]['values']['max'] = $max_value;
                                    } elseif ($max_value > $filters[$value['id']]['values']['max']) {
                                        $filters[$value['id']]['values']['max'] = $max_value;
                                    }
                                    if ($min_value > $filters[$value['id']]['values']['max']) {
                                        $filters[$value['id']]['values']['max'] = $min_value;
                                    }
                                } else {
                                    if (!in_array($value['item_option_value_min'], $filters[$value['id']]['values'])) {
                                        $filters[$value['id']]['values'][] = $value['item_option_value_min'];
                                    }
                                }
                            }
                        }
                        Yii::$app->cache->set(
                            [
                                'brand_filters',
                                $brand->id,
                            ],
                            $filters,
                            0,
                            new TagDependency(['tags' => 'db_cache_catalog'])
                        );
                    }
                    $data['filters'] = $filters;
                    $sel_filter = SArrayHelper::getValue($params_request, 'filters', []);
                    //TODO тут надо проверить с нормальными фильтрами, возможно разрез с логикой работы
                    if ($sel_filter) {
                        $filter_conditions = [];
                        foreach ($sel_filter as $key => $value) {
                            if (isset($filters[$key])) {
                                Category::modifyQueryFilter($filters[$key], $value, [$q], $filter_conditions);
                            }
                        }
                        if ($filter_conditions) {
                            $q->andWhere($filter_conditions);
                        }
                        $data['sel_filter'] = $sel_filter;
                    }
                }
                $types = Yii::$app->request->get('types');

                if ($types) {
                    if (isset($types['isHit'])) {
                        $q->andWhere(['`items`.isHit' => 1]);
                        $q_filter->andWhere(['`items`.isHit' => 1]);
                    }
                    if (isset($types['popularity'])) {
                        $q->andWhere('`items`.popularity>0');
                        $q_filter->andWhere('`items`.popularity>0');
                    }
                    if (isset($types['isSale'])) {
                        $q->andWhere('`items`.old_price is not NULL');
                        $q_filter->andWhere('`items`.old_price is not NULL');
                    }
                    if (isset($types['isNew'])) {
                        $q->andWhere(['`items`.isNew' => 1]);
                        $q_filter->andWhere(['`items`.isNew' => 1]);
                    }
                    $data['types'] = $types;
                }
                $q_price = clone $q;
                $q_price->orderBy = null;
                $q_price->andWhere(['>', '`items`.`price`', 0]);
                $q_price->select(
                    [
                        'max' => new yii\db\Expression('MAX(`items`.`price`)'),
                        'min' => new yii\db\Expression('MIN(`items`.`price`)'),
                    ]
                );
                $price_db = $q_price->createCommand()->queryOne();
                if ($price_db) {
                    $data['max_price'] = floatval($price_db['max']);
                    $data['min_price'] = floatval($price_db['min']);
                }

                if ($enable_filter) {
                    $sel_min_price = SArrayHelper::getValue($params_request, 'price_min', false);
                    $sel_max_price = SArrayHelper::getValue($params_request, 'price_max', false);
                    if ($sel_min_price && $sel_max_price) {
                        $start_price = (int) $sel_min_price;
                        $end_price = (int) $sel_max_price;
                        if ($start_price != $end_price && $data['max_price'] != $data['min_price']) {
                            $data['start_price'] = $start_price;
                            $data['end_price'] = $end_price;
                            $q->andWhere(
                                [
                                    'and',
                                    ['>=', 'price', (int) $sel_min_price],
                                    ['<=', 'price', (int) $sel_max_price],
                                ]
                            );
                        }
                    } else {
                        $data['start_price'] = $data['min_price'];
                        $data['end_price'] = $data['max_price'];
                    }
                }
                $count = $q->count('id');
                $order = \Yii::$app->request->get('order', 'price_asc');
                switch ($order) {
                    case 'price_asc':
                        $q->orderBy(
                            [
                                '`items`.`price`>0' => SORT_DESC,
                                '`items`.price' => SORT_ASC,
                            ]
                        );
                        $data['order'] = $order;
                        break;
                    case 'price_desc':
                        $q->orderBy(
                            [
                                '`items`.`price`>0' => SORT_DESC,
                                '`items`.price' => SORT_DESC,
                            ]
                        );
                        $data['order'] = $order;
                        break;
                    case 'popularity':
                        $q->orderBy(['`items`.popularity' => SORT_DESC]);
                        $data['order'] = $order;
                        break;
                    case 'new':
                        $q->orderBy(['`items`.isNew' => SORT_DESC]);
                        $data['order'] = $order;
                        break;
                    case 'name_asc':
                        $q->orderBy(['`items`.name' => SORT_ASC]);
                        $data['order'] = $order;
                        break;
                    default:
                        $q->orderBy(['`items`.price' => SORT_ASC]);
                        $order = 'price_asc';
                        $data['order'] = $order;
                        break;
                }
                if ($order != 'price_asc') {
                    $url_params['order'] = $order;
                }
                $data['model'] = new Items();
                $data['url_params'] = $url_params;
                $data['params_request'] = $params_request;
                $pages = new Pagination(['totalCount' => $count]);
                if (Yii::$app->request->get('page_all', 0) != 0) {
                    $data['page_all'] = Yii::$app->request->get('page_all');
                } else {
                    $pages->setPageSize(200);

                    $q->offset($pages->offset)
                        ->limit($pages->limit);
                }
                $currentPage = $pages->getPage();
                $pageCount = $pages->getPageCount();
                if ($pages->getPageCount() > 1 && $currentPage < $pageCount - 1) {
                    $url_pagination = $pages->createUrl($currentPage + 1, null, false);
                } else {
                    $url_pagination = '';
                }
                $q->with(
                    [
                        'itemImgs',
                    ]
                );
                $options_list = [];
                $q->with['itemOptionsValues'] = function ($q) use ($cats_a) {
                    /** @var \yii\db\ActiveQuery $q */
                    $q->with('option');
                    $q->join(
                        'LEFT JOIN',
                        'options_category',
                        '`options_category`.`option_id`= `item_options_value`.option_id'
                    );
                };
                $items = $q->all();
                $pagination = LinkPager::widget(
                    [
                        'pagination' => $pages,
                        'options' => [
                            'class' => 'Pagination',
                            'data-block' => 'pagination_items',
                        ],
                        'prevPageCssClass' => '__prev',
                        'nextPageCssClass' => '__next',
                        'activePageCssClass' => '__current',
                        'disabledPageCssClass' => '__hidden',
                        'nextPageLabel' => '',
                        'prevPageLabel' => '',
                        'registerLinkTags' => true,
                    ]
                );
                $currentPage++;
                if (Yii::$app->request->isAjax) {
                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    $clone_url = $url_params;
                    if ($params_request) {
                        $clone_url['filter'] = Items::parseEncode($params_request);
                    }
                    if ($currentPage > 1) {
                        $clone_url['page'] = $currentPage;
                    }

                    return [
                        'url_pagination' => $url_pagination,
                        'pagination' => $pagination,
                        'current_page' => $currentPage,
                        'url' => Url::to($clone_url),
                        'items' => $this->renderPartial(
                            '//blocks/items',
                            ['items' => $items, 'options_list' => $options_list]
                        ),
                    ];
                }
                $data['url_pagination'] = $url_pagination;
                $data['pagination'] = $pagination;
                $data['currentPage'] = $currentPage;
                $data['items'] = $items;
                $data['options_list'] = $options_list;

                $data['brand'] = $brand;


                $this->breadcrumbs[] = [
                    'label' => $brand->name,
                    'url' => ['site/brands', 'id' => $brand->id],
                ];

                return $this->render('brand_one', $data);
            } else {
                throw new BadRequestHttpException('Данный бренд не найден');
            }
        } else {
            $this->SeoSettings('module', 9, 'Бренды');
            $data = [];
            $q = Brands::find()->where(['isVisible' => 1]);
            $count = $q->count();
            $pages = new Pagination(['totalCount' => $count]);
            $pages->setPageSize(12);
            $data['brands'] = $q->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
            $data['pages'] = $pages;
            return $this->render('brands', $data);
        }
    }

    public function actionNews()
    {
        $this->breadcrumbs[] = [
            'label' => 'Новости',
            'url' => ['site/news'],
        ];
        if ($id = Yii::$app->request->get('id')) {
            /**
             * @var $item News
             */
            if ($item = News::find()->andWhere(['id' => intval($id), 'isVisible' => 1])->one()) {
                $this->SeoSettings('news', $item->id, $item->name);
                Yii::$app->opengraph->set([
                    'title' => $item->name,
                    'image' => Yii::$app->request->getHostInfo() . $item->img,
                    'type' => 'Новости',
                ]);
                $this->breadcrumbs[] = [
                    'label' => $item->name,
                    'url' => ['site/news', 'id' => $item->id],
                ];
                $news = News::find()
                    ->andWhere(['<>', 'id', $item->id])
                    ->andWhere(['isVisible' => 1])
                    ->andWhere(['>=', 'created_at', $item->created_at - 2629743])
                    ->andWhere(['<=', 'created_at', $item->created_at + 2629743])
                    ->limit(4)
                    ->all();
                return $this->render('news_one', ['item' => $item, 'news' => $news]);
            } else {
                throw new BadRequestHttpException('Данная новость не найдена');
            }
        } else {
            $this->SeoSettings('module', 7, 'Новости');
            $data = [
                'years' => [],
                'select_year' => ''
            ];
            $select_year = Yii::$app->request->get('year');
            $years = Yii::$app->cache->get('news_years');
            if (!$years) {
                $years = [];
                /**
                 * @var $news_years News[]
                 */
                $news_years = News::find()->select('created_at')->orderBy(['created_at' => SORT_DESC])->where(['isVisible' => 1])->all();
                $i = 0;
                foreach ($news_years as $key => $news_year) {
                    $year = date('Y', $news_year->created_at);
                    if (!isset($years[$year])) {
                        $years[$year] = $i++;
                    }
                }
                $years = array_flip($years);
                Yii::$app->cache->set('news_years', $years);
            }
            if ($years) {
                $data['years'] = $years;
                $main_year = $years[0];
                $data['main_year'] = $main_year;
            }
            $q = News::find()->orderBy(['created_at' => SORT_DESC])->where(['isVisible' => 1]);
            if ($select_year && in_array((int) $select_year, $years)) {
                $data['select_year'] = $select_year;
                $q->andWhere(['>=', 'created_at', strtotime('01.01.' . $select_year . ' 00:00:00')]);
                $q->andWhere(['<=', 'created_at', strtotime('31.12.' . $select_year . ' 23:59:59')]);
            } else {
                if ($select_year) {
                    return $this->redirect(['site/news']);
                } else {
                    if (isset($main_year)) {
                        $q->andWhere(['>=', 'created_at', strtotime('01.01.' . $main_year . ' 00:00:00')]);
                        $q->andWhere(['<=', 'created_at', strtotime('31.12.' . $main_year . ' 23:59:59')]);
                    }
                }
            }
            $count = $q->count();
            $pages = new Pagination(['totalCount' => $count]);
            $pages->setPageSize(8);
            $data['items'] = $q->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
            $data['pages'] = $pages;
            return $this->render('news', $data);
        }
    }

    public function actionReviews()
    {
        $this->SeoSettings('module', 4, 'Отзывы');
        $this->breadcrumbs[] = [
            'label' => 'Отзывы',
            'url' => ['site/reviews'],
        ];
        $data = [];
        $data['items'] = Reviews::find()->orderBy(['created_at' => SORT_DESC])->where(['isVisible' => 1])->all();
        return $this->render('reviews', $data);
    }

    public function actionSets()
    {
        $this->SeoSettings('module', 6, 'Сеты');
        $data = [];
        $data['items'] = Sets::find()->orderBy(['created_at' => SORT_DESC])->where(['isVisible' => 1])->all();
        return $this->render('sets', $data);
    }

    public function actionSet($id)
    {
        if ($item = Sets::find()->andWhere(['id' => intval($id), 'isVisible' => 1])->one()) {
            $this->SeoSettings('sets', $item->id, $item->name);
            Yii::$app->opengraph->set([
                'title' => $item->name,
                'image' => Yii::$app->request->getHostInfo() . $item->img,
                'type' => 'Сеты',
            ]);
            $this->breadcrumbs[] = [
                'label' => 'Сеты',
                'url' => ['site/sets'],
            ];
            $this->breadcrumbs[] = [
                'label' => $item->name,
                'url' => ['site/set', 'id' => $item->id],
            ];
            $data = [
                'item' => $item
            ];
            return $this->render('sets_one', $data);
        } else {
            throw new BadRequestHttpException('Данный сет не найден');
        }
    }

    public function actionJobs()
    {
        $this->SeoSettings('module', 5, 'Вакансии');
        $this->breadcrumbs[] = [
            'label' => 'Вакансии',
            'url' => ['site/jobs'],
        ];
        $data = [];
        $data['items'] = Jobs::find()->orderBy(['created_at' => SORT_DESC])->where(['isVisible' => 1])->all();
        return $this->render('jobs', $data);
    }

    public function actionBasket()
    {
        $this->SeoSettings(false, false, 'Корзина');

        $sum = 0;
        $weight = 0;
        $delivery = -1;

        $items = $sets = $discount = [];
        if ($this->cart_items) {
            $q = new ActiveQuery(Items::className());
            $q->indexBy('id')
                ->andWhere(['id' => array_keys($this->cart_items)])->with(['itemsTypeHandlings.typeHandling', 'itemsTogethers']);
            $items = $q->all();

            if (!Yii::$app->user->isGuest && doubleval(Yii::$app->user->identity->discount)) {
                $discount = [];
            } else {
                $discount = $this->function_system->discount_sale_items($items, $this->cart_items);
            }

            if (!empty($items)) {
                foreach ($items as $item) {
                    $count = $this->cart_items[$item->id];
                    $sum += $this->function_system->full_item_price($discount, $item, $count);
                    $weight += $item->weight * $count;
                }
            }
        }

        if ($this->cart_sets) {
            $q = new ActiveQuery(Sets::className());
            $q->indexBy('id')
                ->andWhere(['id' => array_keys($this->cart_sets)]);
            $sets = $q->all();

            if (!empty($sets)) {
                foreach ($sets as $set) {
                    $count = $this->cart_sets[$item->id];
                    $sum += round($count * $item->real_price());
                }
            }
        }

        if (!empty($this->city) && (int) $this->city > 0) {
            $delivery = (new Delivery())->getDelivery($sum, $weight, (int) $this->city);
        }

        return $this->render('cart', [
            'items' => $items,
            'sets' => $sets,
            'discount' => $discount,
            'delivery' => $delivery
        ]);
    }

    public function actionOrder()
    {
        //d::td('---');
        if ($this->cart_count) {
            $this->SeoSettings(false, false, 'Оформление заказа');
            if (Yii::$app->request->isPost) {
                $itemsSession = Yii::$app->request->post('items', []);
                $type_handling = Yii::$app->request->post('type_handling', []);
                $setsSession = Yii::$app->request->post('sets', []);

                Yii::$app->session->set('items', $itemsSession);
                Yii::$app->session->set('type_handling', $type_handling);
                Yii::$app->session->set('sets', $setsSession);
            } else {
                $itemsSession = Yii::$app->session->get('items', []);
                $type_handling = Yii::$app->session->get('type_handling', []);
                $setsSession = Yii::$app->session->get('sets', []);
            }

            $sum = 0;
            $weight = 0;
            $delivery = -1;

            if ($itemsSession) {
                $items = Items::find()
                    ->indexBy('id')
                    ->where(['id' => array_keys($itemsSession)])
                    ->with(['itemsTypeHandlings.typeHandling', 'itemsTogethers'])
                    ->all();

                if (!Yii::$app->user->isGuest && doubleval(Yii::$app->user->identity->discount)) {
                    $discount = [];
                } else {
                    $discount = $this->function_system->discount_sale_items($items, $itemsSession);
                }

                if (!empty($items)) {
                    foreach ($items as $item) {
                        $count = $this->cart_items[$item->id];
                        $sum += $this->function_system->full_item_price($discount, $item, $count);
                        $weight += $item->weight * $count;
                    }
                }
            } else {
                $items = $discount = [];
            }

            if ($setsSession) {
                $q = Sets::find();
                $q->indexBy('id')
                    ->andWhere(['id' => array_keys($setsSession)]);
                $sets = $q->all();

                if (!empty($sets)) {
                    foreach ($sets as $set) {
                        $count = $this->cart_sets[$item->id];
                        $sum += round($count * $item->real_price());
                    }
                }
            } else {
                $sets = [];
            }

            if ($sum > 0 && !Yii::$app->user->isGuest && doubleval(Yii::$app->user->identity->discount)) {
                $order = new Orders(['discount' => Yii::$app->user->identity->discount . '%']);
                $sum = $sum - $order->discount($sum);
            }

            $payments = (new Order())->getData_payment();

            $cities = City::find()->all();
            $pickpoints = Pickpoint::find()->where(['active' => true])->all();
            $cityInfo = [];

            foreach ($cities as $c) {

                $deliveryCurrent = (new Delivery())->getDelivery($sum, $weight, $c->id);

                if ($deliveryCurrent > 0) {
                    $deliveryText = number_format($deliveryCurrent, 0, '', ' ') . ' т.';
                } elseif ($deliveryCurrent == 0) {
                    $deliveryText = 'Бесплатная';
                } else {
                    $deliveryText = 'Самовывоз';
                }

                $sumAll = $sum + ($deliveryCurrent > 0 ? $deliveryCurrent : 0);
                $pickupPrice = (new Delivery())->getPickUpPrice($c->id);

                $pickup = (
                    (
                        ($c->isOnlyPickup || $deliveryCurrent == -1) &&
                        (empty($c->delivery_free_sum) || $c->delivery_free_sum == 0)
                    ) ? true : false
                );

                $pickup_sum_all = ($sum + $pickupPrice);
                $cityInfo[$c->id] = [
                    'id' => $c->id,
                    'pickup_switcher' => $c->pickup_switcher,
                    'text_courier' => $c->pickup,
                    'only_pickup' => $pickup,
                    'coordinate' => $c->coordinate,
                    'delivery' => $deliveryText,
                    'delivery_price' => $deliveryCurrent,
                    'sum_all' => number_format($sumAll, 0, '', ' ') . ' т.',
                    'pickpoint' => null,
                    'isYandexDelivery' => $c->isYandexDelivery,
                    'name' => $c->name,
                    'pickup_price' => $pickupPrice,
                    'pickup_sum_all' => number_format($pickup_sum_all, 0, '', ' ') . ' т.',
                    'sum_all_number' => $pickup_sum_all,
                    'courier_price' => (
                        $c->delivery_free_sum
                        && $c->delivery_free_sum > 0
                        && $sum < $c->delivery_free_sum ? $c->delivery_free_sum : 0
                    ),
                    'courier_price_text' => 'С ' . number_format($c->delivery_free_sum, 0, '', ' ') . ' тг бесплатная доставка',
                    'min_order_sum' => ($c->getMinOrderAmount($c->id))
                ];

                $cityInfo[$c->id]['payment'] = [];

                if (!empty($c->payment_type)) {
                    foreach ($c->payment_type as $pay) {
                        if ($key = array_search(Yii::$app->settings->get($pay), $payments)) {
                            $cityInfo[$c->id]['payment'][$key] = Yii::$app->settings->get($pay);
                        }
                    }
                }
                if (!count($cityInfo[$c->id]['payment'])) {
                    $cityInfo[$c->id]['payment'] = $payments;
                }

                if (
                    !Yii::$app->user->isGuest
                    and Yii::$app->user->identity->isWholesale != NULL
                    and Yii::$app->user->identity->isWholesale > 0
                ) {

                    // Сбрасываем массив, собранный для обычного пользователя.
                    $cityInfo[$c->id]['payment'] = [];

                    $user_opt_payments_value_indexes = EditUser::getData_payment(true);
                    $user_opt_payments = EditUser::getData_payment();

                    /*
                     * Вынес условие проверки на пустоту ...user->payment_type в отдельный if.
                     * Вдруг для оптовиков нужно будет ещё что-то делать в текущем if.
                     */
                    if(count(Yii::$app->user->identity->payment_type)){
                        foreach(Yii::$app->user->identity->payment_type as $pay_type){
                            $pay_type_number = $user_opt_payments_value_indexes[$pay_type];
                            $cityInfo[$c->id]['payment'][$pay_type_number] = $user_opt_payments[$pay_type];
                        }
                    }

                    if (!count($cityInfo[$c->id]['payment'])) {
                        $opt_payments = [];
                        foreach($user_opt_payments as $payment_name => $payment_text){
                            $opt_payments[$user_opt_payments_value_indexes[$payment_name]] = $payment_text;
                        }
                        $cityInfo[$c->id]['payment'] = $opt_payments;
                    }
                }

                // Сортировка по возрастанию
                ksort($cityInfo[$c->id]['payment']);

                if (!empty($pickpoints)) {
                    foreach ($pickpoints as $pickpoint) {
                        if ($pickpoint->city_id == $c->id) {
                            $cityInfo[$c->id]['pickpoint'][$pickpoint->id] = [
                                'id' => $pickpoint->id,
                                'name' => $pickpoint->name,
                                'desc' => $pickpoint->desc,
                                'coordinate' => $pickpoint->coordinate,
                                'delivery' => 'Самовывоз',
                                'delivery_price' => 0,
                                'sum_all' => number_format($sum, 0, '', ' ') . ' т.',
                            ];
                        }
                    }
                }

                if (!empty($this->city) && (int) $this->city > 0 && $c->id == (int) $this->city) {
                    $delivery = $deliveryCurrent;
                    $paymentType = $cityInfo[$c->id]['payment'];
                }

            } //foreach

//            d::pri($cityInfo);

            $data = [
                'items' => $items,
                'sets' => $sets,
                'discount' => $discount,
                'type_handling_session' => $type_handling,
                'delivery' => $delivery,
                'paymentType' => $paymentType,
                'cityInfo' => Json::encode($cityInfo),
                'cityId' => (int) $this->city
            ];
            //            d::pex($cityInfo);
            return $this->render('order', $data);
        } else {
            return $this->redirect(['site/index']);
        }
    }

    public function actionSuccessOrder()
    {
        if ($id = \Yii::$app->session->get('success_order')) {
            //        $id = 103857;
//        if (1) {
            $get = Yii::$app->request->get();
            /** @var $item Pages */
            $item = Pages::findOne(3);
            $this->SeoSettings(false, false, 'Спасибо за покупку!');
            if ($success_order_pay = \Yii::$app->session->get('success_order_pay')) {
                $order_model = Orders::findOne(intval($success_order_pay));
                if ($order_model) {
                    /**
                     * @var $mailer \yii\swiftmailer\Message
                     */
                    $send_mails = explode(',', \Yii::$app->settings->get('manager_emails', 'viktor@instinct.kz'));
                    foreach ($send_mails as $key_email => &$value_email) {
                        if (!($value_email = trim($value_email, " \t\n\r\0\x0B"))) {
                            unset($send_mails[$key_email]);
                        }
                    }
                    \Yii::$app->mailer->compose(['html' => 'admin/order'], ['order' => $order_model])
                        ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->params['siteName'] . ' info'])
                        ->setTo($send_mails)
                        ->setSubject('Новый заказ на сайте ' . \Yii::$app->params['siteName'])->send();
                    if ($order_model->user_mail) {
                        \Yii::$app->mailer->compose(['html' => 'order'], ['order' => $order_model])
                            ->setFrom([\Yii::$app->params['supportEmail'] => 'Интернет-магазин ' . \Yii::$app->params['siteName'] . '.kz'])
                            ->setTo($order_model->user_mail)
                            ->setSubject('Заказ на сайте ' . \Yii::$app->params['siteName'] . '.kz')->send();
                    }

                    \Yii::$app->session->remove('success_order_pay');
                    if(!isset($get['pay_fail'])){
                        Orders::updateAll(['pay_status' => 'send_pay'], ['id' => $order_model->id, 'pay_status' => 'wait']);
                    }
                }
                if (!d::isLocal()) {
                    Yii::$app->session->remove('items');
                    Yii::$app->session->remove('type_handling');
                    Yii::$app->session->remove('sets');
                }
            }

            $item->body = str_replace('{order_number}', $id, $item->body);

            if (!Yii::$app->user->isGuest) {
                $user = Yii::$app->user->identity;
                if ($user->isWholesale > 0) {
                    $item->body .= '<br><a href="' . Url::to(['user/print-invoice-payment', 'id' => $id]) . '" target="_blank">'
                        . Html::button(
                            'Счёт для оплаты',
                            ['class' => 'btn_Form blue send invoice-print']
                        ) . '</a>';
                }
            }

            if (isset($get['pay_fail']) and $get['pay_fail'] == '1') {
                $item->name = preg_replace('|{.+}|i', 'заказ', $item->name);
                $item->body .= '<br><div style="font-weight: 600;font-size:22px;color:red;">Внимание</div>Ваш заказ создан, но не оплачен.<br>Оплатить заказ вы сможете через кнопку "Оплата" в истории заказов.<br><a href="' . Url::to(['user/orders', 'id' => $id]) . '" target="_blank">Перейти в заказ</a>';
            }

            $item->name = preg_replace('~({|})~i', '', $item->name);

            /*
            $phone = $order_model->user_phone;
            $order_id = $order_model->id;
            $full_price = $order_model->full_price;
            SmsController::send_sms("$phone", "Вами оплачен заказ № $order_id на kingfisher.kz (Сумма заказа $full_price)");
            */

            return $this->render('success_order', ['item' => $item]);
        } else {
            throw new BadRequestHttpException();
        }
    }

    public function actionSearch($query)
    {
        $this->SeoSettings(false, false, 'Результаты поиска');
        $data = [
            'items' => [],
            'news' => [],
            'query' => $query
        ];
        $q = new ActiveQuery(Items::className());
        $q->andWhere([
            'OR',
            ['like', 'items.name', $query],
            ['like', 'items.body_small', $query],
            ['like', 'items.tags', $query],
            ['like', '`category`.name', $query],
        ]);
        $q->join('LEFT JOIN', 'category', '`items`.cid=`category`.id');
        $q->andWhere(['`items`.isVisible' => 1, 'category.isVisible' => 1]);

        if (Yii::$app->request->isAjax) {

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $result = [
                'items' => '',
                'cats' => '',
                'count' => 0
            ];
            if (!$query) {
                return $result;
            }
            $count = $q->count();
            if ($count) {
                $result['count'] = '(' . strip_tags(Yii::t('main', 'count_items', ['n' => $count])) . ')';
                $q->limit(4);
                $items = $q->all();
                $result['items'] = $this->renderPartial('//blocks/items_search', ['items' => $items]);
            }

            $cats = Category::find()->where(['isVisible' => 1])->andWhere(
                [
                    'OR',
                    ['like', '`name`', $query]
                ]
            )->limit(3)->all();

            if ($cats) {
                $result['cats'] = $this->renderPartial('//blocks/cats_search', ['items' => $cats]);
            }

            $brands = Brands::find()->where(['isVisible' => 1])->andWhere(
                [
                    'OR',
                    ['like', '`name`', $query]
                ]
            )->limit(3)->all();

            if (isset($brands)) {
                $result['brands'] = $this->renderPartial('//blocks/brands_search', ['brands' => $brands]);
            }


            return $result;
        }

        $data['items'] = $q->all();
        $q = new ActiveQuery(News::className());
        $q->andWhere([
            'OR',
            ['like', 'name', $query],
            ['like', 'body', $query],
            ['like', 'small_body', $query],
        ]);
        $q->andWhere(['isVisible' => 1]);
        $data['news'] = $q->all();
        return $this->render('search', $data);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout(false);
        return $this->goHome();
    }

    public function actionRegistration()
    {
        $this->breadcrumbs[] = [
            'label' => 'Регистрация',
            'url' => ['site/registration'],
        ];
        $this->SeoSettings(false, false, 'Регистрация');
        return $this->render('registration');
    }

    public function actionRecoveryPassword()
    {
        $this->breadcrumbs[] = [
            'label' => 'Восстановление пароля',
            'url' => ['site/recovery-password'],
        ];
        $this->SeoSettings(false, false, 'Восстановление пароля');
        return $this->render('recovery');
    }

    public function actionResetPassword($token)
    {
        /**
         * @var $user User
         */
        $user = User::findByPasswordResetToken($token);
        if ($user) {
            $password = Yii::$app->security->generateRandomString(6);
            $user->password = $password;
            $user->removePasswordResetToken();
            if ($user->save(false)) {
                \Yii::$app->mailer->compose(['html' => 'new_password'], ['user' => $user, 'password' => $password])
                    ->setFrom([\Yii::$app->params['supportEmail'] => 'Интернет-магазин ' . \Yii::$app->params['siteName'] . '.kz'])
                    ->setTo($user->email)
                    ->setSubject('Новый пароль для сайта ' . \Yii::$app->params['siteName'] . '.kz')
                    ->send();
                $data = [];
                $item = new Pages();
                $item->body = 'Новый пароль отправлен на ваш E-Mail';
                $item->name = 'Восстановление пароля';
                $data['item'] = $item;
                return $this->render('page', $data);
            } else {
                throw new BadRequestHttpException();
            }
        } else {
            throw new BadRequestHttpException();
        }
    }

    public function onAuthSuccess($client)
    {
        /**
         * @var \yii\authclient\clients\Facebook $client
         */
        $attributes = $client->getUserAttributes();
        /** @var Auth $auth */
        $auth = Auth::find()->where([
            'source' => $client->getId(),
            'source_id' => $attributes['id'],
        ])->one();
        if (Yii::$app->user->isGuest) {
            if ($auth) { // login
                $user = $auth->user;
                Yii::$app->user->login($user);
            } else { // signup
                $emails = [];
                if (isset($attributes['emails']) && $attributes['emails']) {
                    $emails = ArrayHelper::getColumn($attributes['emails'], 'value');
                } else {
                    if (isset($attributes['email'])) {
                        $emails[] = $attributes['email'];
                    }
                }
                if ($emails && ($user = User::find()->where(['email' => $emails])->one())) {
                    $auth = new Auth([
                        'user_id' => $user->id,
                        'source' => $client->getId(),
                        'source_id' => (string) $attributes['id'],
                    ]);
                    $auth->save();
                    Yii::$app->user->login($user);
                } else {
                    $name = '';
                    if (!isset($attributes['name'])) {
                        if (isset($attributes['displayName'])) {
                            $name = $attributes['displayName'];
                        }
                    } else {
                        $name = $attributes['name'];
                    }
                    $email = '';
                    if ($emails) {
                        $email = $emails[0];
                    }
                    $password = Yii::$app->security->generateRandomString(6);
                    $user = new User([
                        'username' => $name,
                        'email' => $email,
                        'password' => $password,
                    ]);
                    $user->generateAuthKey();
                    $user->generatePasswordResetToken();
                    $transaction = $user->getDb()->beginTransaction();
                    if ($user->save()) {
                        $auth = new Auth([
                            'user_id' => $user->id,
                            'source' => $client->getId(),
                            'source_id' => (string) $attributes['id'],
                        ]);
                        if ($auth->save()) {
                            if ($user->email) {
                                \Yii::$app->function_system->send_promo_code('reg', $user->email);
                            }
                            $transaction->commit();
                            Yii::$app->user->login($user);
                        } else {
                            print_r($auth->getErrors());
                        }
                    } else {
                        print_r($user->getErrors());
                    }
                }
            }
        } else { // user already logged in
//            if (!$auth) { // add auth provider
//                $auth = new Auth([
//                    'user_id' => Yii::$app->user->id,
//                    'source' => $client->getId(),
//                    'source_id' => $attributes['id'],
//                ]);
//                $auth->save();
//            }
        }
        $options['success'] = true;
        $data['options'] = $options;
        $response = Yii::$app->getResponse();
        $response->content = $this->view->render('//redirect', $data);
        return $response;
    }
    //    public function actionLogin()
//    {
//        if (!\Yii::$app->user->isGuest) {
//            return $this->goHome();
//        }
//        $model = new LoginForm();
//        if ($model->load(Yii::$app->request->post()) && $model->login()) {
//            return $this->goBack();
//        } else {
//            return $this->render('login', [
//                'model' => $model,
//            ]);
//        }
//    }
//    public function actionRequestPasswordReset()
//    {
//        $model = new PasswordResetRequestForm();
//        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//            if ($model->sendEmail()) {
//                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');
//                return $this->goHome();
//            } else {
//                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
//            }
//        }
//        return $this->render('requestPasswordResetToken', [
//            'model' => $model,
//        ]);
//    }
//
//    public function actionResetPassword($token)
//    {
//        try {
//            $model = new ResetPasswordForm($token);
//        } catch (InvalidParamException $e) {
//            throw new BadRequestHttpException($e->getMessage());
//        }
//        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
//            Yii::$app->getSession()->setFlash('success', 'New password was saved.');
//            return $this->goHome();
//        }
//        return $this->render('resetPassword', [
//            'model' => $model,
//        ]);
//    }

    public function actionPromo($code)
    {
        $this->breadcrumbs[] = [
            'label' => 'Промо',
            'url' => ['site/promo', ['code' => $code]],
        ];
        $this->SeoSettings(false, false, 'Промо');
        $specialActionCode = SpecActionCode::find()->where(['uuid' => $code, 'status' => 0])->one();
        if ($specialActionCode && $specialActionCode->checkValid()) {
            return $this->render('promo', ['code' => $code]);
        } else {
            return $this->render('promo_text', ['status' => 'empty']);
        }
    }

    /**
     * @param $code
     * @return array|bool
     * @throws BadRequestHttpException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionSendPromo($code)
    {
        $form = new PromoRegistration();
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $specialActionCode = SpecActionCode::find()->where(['uuid' => $code, 'status' => 0])->one();
            if (!($specialActionCode && $specialActionCode->checkValid())) {
                return [
                    'success' => true,
                    'url' => Url::to(['site/promo', 'code' => $code])
                ];
            }
            $specialActionCodes = SpecActionCode::find()
                ->where(['spec_action_id' => $specialActionCode->spec_action_id])
                ->select('id')->column();
            //проверяем не забирал ли кто уже приз по данному номеру
            if (
                SpecActionPhone::find()->where([
                    'spec_action_code_id' => $specialActionCodes,
                    'phone' => $form->phone,
                    'status' => 1
                ])->exists()
            ) {
                return [
                    'errors' => [
                        'promoregistration-phone' => [
                            'Данный номер уже участвовал в акции'
                        ]
                    ]
                ];
            }
            $result = [];
            $errors = ActiveForm::validate($form);
            if ($errors) {
                $result['errors'] = $errors;
            } else {
                $specActionPhoneId = $form->send($specialActionCode);
                \Yii::$app->session->set('promo_id_' . $code, $specActionPhoneId);
                return [
                    'success' => true,
                    'url' => Url::to(['site/enter-code', 'code' => $code])
                ];
            }
            return $result;
        } else {
            throw new BadRequestHttpException('not found', 404);
        }
    }

    /**
     * @param $code
     * @return array|string
     * @throws BadRequestHttpException
     */
    public function actionEnterCode($code)
    {
        $specialActionCode = SpecActionCode::find()->where(['uuid' => $code, 'status' => 0])->one();
        if (!($specialActionCode && $specialActionCode->checkValid())) {
            return $this->redirect(Url::to(['site/promo', 'code' => $code]), 302);
        }
        $specActionPhoneId = \Yii::$app->session->get('promo_id_' . $code);

        if ($specActionPhoneId) {
            $specActionPhone = SpecActionPhone::find()->where([
                'id' => $specActionPhoneId
            ])->one();
            $specialActionCodes = SpecActionCode::find()
                ->where(['spec_action_id' => $specialActionCode->spec_action_id])
                ->select('id')->column();
            //проверяем не забирал ли кто уже приз по данному номеру
            if (
                SpecActionPhone::find()->where([
                    'spec_action_code_id' => $specialActionCodes,
                    'phone' => $specActionPhone->phone,
                    'status' => 1
                ])
                    ->andWhere([
                        '<>',
                        'id',
                        $specActionPhone->id
                    ])
                    ->exists()
            ) {
                return $this->redirect(Url::to(['site/promo', 'code' => $code]), 302);
            }
            if ($specActionPhone->status == 1) {
                return $this->render('promo_text_used');
            }
            $this->breadcrumbs[] = [
                'label' => 'Промо',
                'url' => ['site/promo', ['code' => $code]],
            ];
            $this->SeoSettings(false, false, 'Промо');
            return $this->render('promo_enter_code', ['code' => $code]);
        } else {
            throw new BadRequestHttpException('not found', 404);
        }

    }

    /**
     * @param $code
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionSendCode($code)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $specialActionCode = SpecActionCode::find()->where(['uuid' => $code, 'status' => 0])->one();
        if (!($specialActionCode && $specialActionCode->checkValid())) {
            return [
                'success' => true,
                'url' => Url::to(['site/promo', 'code' => $code])
            ];
        }

        $specActionPhoneId = \Yii::$app->session->get('promo_id_' . $code);

        if ($specActionPhoneId) {
            $specActionPhone = SpecActionPhone::find()->where([
                'id' => $specActionPhoneId
            ])->one();

            $specialActionCodes = SpecActionCode::find()
                ->where(['spec_action_id' => $specialActionCode->spec_action_id])
                ->select('id')->column();

            //проверяем не забирал ли кто уже приз по данному номеру
            if (
                SpecActionPhone::find()->where([
                    'spec_action_code_id' => $specialActionCodes,
                    'phone' => $specActionPhone->phone,
                    'status' => 1
                ])
                    ->andWhere([
                        '<>',
                        'id',
                        $specActionPhone->id
                    ])
                    ->exists()
            ) {
                return [
                    'success' => true,
                    'url' => Url::to(['site/promo', 'code' => $code])
                ];
            }
            if ($specActionPhone->status == 1) {
                return [
                    'success' => true,
                    'url' => Url::to(['site/enter-code', 'code' => $code])
                ];
            }
            $form = new PromoEnterCode();
            if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
                $result = [];
                $errors = ActiveForm::validate($form);
                if ($errors) {
                    $result['errors'] = $errors;
                } else {
                    if ($specActionPhone->code != $form->sms_code) {
                        return [
                            'errors' => [
                                'promoentercode-sms_code' => [
                                    'Неправильный код'
                                ]
                            ]
                        ];
                    }
                    $specActionPhone->status = 1;
                    $specActionPhone->save(false);
                    \Yii::$app->session->set('winner_promo_id_' . $code, $specActionPhoneId);
                    \Yii::$app->session->remove('promo_id_' . $code);
                    return [
                        'success' => true,
                        'url' => Url::to(['site/promo-winner', 'code' => $code])
                    ];
                }
                return $result;
            } else {
                throw new BadRequestHttpException('not found', 404);
            }
        } else {
            throw new BadRequestHttpException('not found', 404);
        }
    }

    /**
     * @param $code
     * @return array|string
     * @throws BadRequestHttpException
     */
    public function actionPromoWinner($code)
    {
        if (\Yii::$app->session->get('winner_promo_id_' . $code)) {
            \Yii::$app->session->remove('winner_promo_id_' . $code);
            $specialActionCode = SpecActionCode::find()->where(['uuid' => $code, 'status' => 0])->one();
            if (!$specialActionCode) {
                return $this->redirect(Url::to(['site/promo', 'code' => $code]), 302);

            }
            $this->breadcrumbs[] = [
                'label' => 'Промо',
                'url' => ['site/promo', ['code' => $code]],
            ];
            $this->SeoSettings(false, false, 'Промо');
            if ($specialActionCode->item_id) {
                return $this->render('promo_text_winner', ['item' => $specialActionCode->item]);
            } else {
                return $this->render('promo_text_empty');
            }
        }
        throw new BadRequestHttpException('not found', 404);
    }

    public function actionTest()
    {
        return $this->render('promo_text_winner', ['item' => Items::findOne(37)]);
        //        $order_id = 50;
//        \Yii::$app->mailer->compose(['html' => 'admin/order'], ['order' => \common\models\Orders::findOne($order_id)])
//            ->setFrom([\Yii::$app->params['supportEmail'] => 'Интернет-магазин ' . \Yii::$app->params['siteName'] . '.kz'])
//            ->setTo('developer@instinct.kz')
//            ->setSubject('Заказ на сайте ' . \Yii::$app->params['siteName'] . '.kz')->send();
    }

    public function actionPay($id)
    {
        $data = [
            'id' => $id
        ];
        return $this->render('pay', $data);
    }

    public function actionDebug()
    {
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            d::ajax('actionDebug()');
            if (isset($post['inputs'])) {
                $inputs = d::serializeToArray($post['inputs']);
                $post = array_merge($post, $inputs);
            }
            //            d::ajax($post);

            //Debug files (для tab1 - Debug скрипты)
            if (isset($post['type'])) {
                $result_type = [];
                switch ($post['type']) {
                    case 'onesignal_send_notification':
                        //d::ajax(Yii::$app->user->id);

                        // 19299
                        $one_data = [
                            'header' => $post['header'],
                            'message' => $post['message']
                        ];
                        if ((isset($post['user_id']) and $post['user_id'] != '')) {
                            $one_data['user_ids'] = [(string) $post['user_id']];
                        }
                        OtherDebugger::onesignal($one_data);
                        break;
                    case 'send_maxma':
                        OtherDebugger::maxma();
                        break;
                    default:
                        $result_type = 'Ничего не произошло';
                }
                d::ajax($result_type);
            }
            d::ajax('Не задан name кнопки');
        } else {
            return $this->render('debug');
        }
    }

    public function actionUpdatedeleted()
    {
        $query = "SELECT `id`, user.username, user.phone, user.email, user.bonus, user.order_sum FROM user INNER JOIN (SELECT `phone` FROM `user` GROUP BY `phone` HAVING COUNT(*) > 1) dup ON user.phone != '' AND user.phone = dup.phone";

        $users_result = Yii::$app->db->createCommand($query)->queryAll();
//        d::ajax($users_result);


    }
}