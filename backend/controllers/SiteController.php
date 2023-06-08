<?php
namespace backend\controllers;

use common\components\Debugger as d;
use backend\models\FooterMenu;
use backend\models\Pages;
use backend\models\SUser;
use common\models\Actions;
use common\models\Banners;
use common\models\Brands;
use common\models\Callback;
use common\models\Cuisine;
use common\models\Jobs;
use common\models\Orders;
use common\models\OrdersItems;
use common\models\Recipes;
use common\models\Reviews;
use common\models\ReviewsItem;
use common\models\Structure;
use common\models\Template;
use common\models\TypeRecipes;
use common\models\User;
use shadow\helpers\GeneratorHelper;
use Yii;
use yii\base\ExitException;
use yii\db\Exception;
use yii\db\Query;
use yii\filters\AccessControl;
use backend\AdminController;
use common\models\LoginForm;
use yii\filters\VerbFilter;


/**
 * Site controller
 */
class SiteController extends AdminController
{
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
						'actions' => ['login', 'error','auth', 'test'],
						'allow' => true,
					],
					[
						'allow' => true,
						'roles' => ['loginAdminPanel','copywriter'],
					],

				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
//					'logout' => ['post'],
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
            ],
            'upload'=>[
                'class'=> 'backend\components\UploadAction'
            ],
            'auth' => [
                'class' => 'backend\components\AuthAction',
//                'successCallback' => [$this, 'onAuthSuccess'],
//                'redirectView'=>'@frontend/views/redirect.php'
            ],
            'tab-debug-ajax' => [
                'class' => 'backend\actions\TabsAjaxActions',
                'actions' => [
                    'debug' => 'debug\\Debug',
                    'user' => 'debug\\User',
                    'orders' => 'debug\\Orders',
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    public function actionLogout()
    {
        $is_admin=Yii::$app->session->get('return_admin');
        Yii::$app->user->logout(false);
        $is_login = false;
        if($is_admin){
            /**
             * @var $user SUser
             */
            $user = SUser::findOne($is_admin);
            if ($user) {
                if( Yii::$app->user->login($user, 3600 * 24 * 30)){
                    $is_login = true;
                }
            }
            Yii::$app->session->remove('return_admin');
        }
        if($is_login){
            return $this->redirect(['site/s-users']);
        }else{
            return $this->goHome();
        }
    }
    public function actionSUsers()
    {
        $data['items'] = SUser::find()->orderBy(['id' => SORT_DESC])->all();
        $this->view->title = 'Сотрудники';
        return $this->render('main/s_users',$data);
    }
    public function actionStructure()
    {
        $data['params'] = Structure::getListItems();
        return $this->render('structure',$data);
    }
    public function actionTemplate()
    {
        $data['items'] = Template::find()->orderBy('created_at DESC')->all();
        return $this->render('template',$data);
    }

    public function actionBrands()
    {
        $data['items'] = Brands::find()->orderBy(['id' => SORT_DESC])->all();
        return $this->render('brands',$data);
    }
    public function actionCuisine()
    {
        $this->breadcrumb[] = [
            'url' => ['site/recipes'],
            'label' => 'Рецепты'
        ];
        $this->view->title = 'Виды кухни';
        $this->breadcrumb[] = [
            'url' => ['site/cuisine'],
            'label' => $this->view->title
        ];
        $data['items'] = Cuisine::find()->orderBy(['id' => SORT_DESC])->all();
        return $this->render('cuisine',$data);
    }
    public function actionType_recipes()
    {
        $this->breadcrumb[] = [
            'url' => ['site/recipes'],
            'label' => 'Рецепты'
        ];
        $this->view->title = 'Типы блюд';
        $this->breadcrumb[] = [
            'url' => ['site/type_recipes'],
            'label' => $this->view->title
        ];
        $data['items'] = TypeRecipes::find()->orderBy(['id' => SORT_DESC])->all();
        return $this->render('type_recipes',$data);
    }

    public function actionBanners()
    {
        $this->view->title = 'Баннеры';
        $this->breadcrumb[] = [
            'url' => ['site/banners'],
            'label' => $this->view->title
        ];
        $data['items'] = Banners::find()->orderBy(['id' => SORT_DESC])->all();
        return $this->render('banners',$data);
    }
    public function actionReviewsSite()
    {
        $this->view->title = 'Отзывы сайта';
        $this->breadcrumb[] = [
            'url' => ['site/reviews-site'],
            'label' => $this->view->title
        ];
        $data['items'] = Reviews::find()->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('reviews_site',$data);
    }
    public function actionJobs()
    {
        $this->view->title = 'Вакансии';
        $this->breadcrumb[] = [
            'url' => ['site/jobs'],
            'label' => $this->view->title
        ];
        $data['items'] = Jobs::find()->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('jobs',$data);
    }
    public function actionFooterMenu(){
        $this->view->title = 'Меню';
        $this->breadcrumb[] = [
            'url' => ['site/footer-menu'],
            'label' => $this->view->title
        ];
//        $data['params'] = FooterMenu::find()->orderBy(['sort' => SORT_ASC])->where(['parent_id'=>null])->all();
        $data['params'] = FooterMenu::getListItems();
        return $this->render('footer_menu',$data);
    }
    public function actionCallback()
    {
        $this->view->title = 'Заказ звонка';
        $this->breadcrumb[] = [
            'url' => ['site/'.$this->action->id],
            'label' => $this->view->title
        ];
        $data['items'] = Callback::find()->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('callback',$data);
    }
    public function actionReviewsItem()
    {
        $this->view->title = 'Отзывы товаров';
        $this->breadcrumb[] = [
            'url' => ['site/'.$this->action->id],
            'label' => $this->view->title
        ];
        $data['items'] = ReviewsItem::find()->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('reviews_item',$data);
    }
    public function actionSubscriptions()
    {
        $this->view->title = 'Подписчики';
        $this->breadcrumb[] = [
            'url' => ['site/'.$this->action->id],
            'label' => $this->view->title
        ];
        $q_sub=new Query();
        $q_sub->select(['email'=>'subscriptions.email']);
        $q_sub->from('subscriptions');
        $subs = $q_sub->all();
        $data = [
            'items' => $subs,
        ];
        return $this->render('//modules/subscriptions',$data);
    }

    /*public function actionTest()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $user = SUser::findOne(true);

        if ($user) {
            Yii::$app->user->login($user, 3600 * 24 * 30);
        }

        return $this->goHome();
    }*/


    /**
     * @throws Exception
     * @throws ExitException
     */
    public function actionAdminDebug()
    {
        if(!Yii::$app->user->isGuest){
            $post = Yii::$app->request->post();
            $get = Yii::$app->request->get();
            if(!Yii::$app->request->isAjax){

                if(isset($get['export_users'])) {

                    $model = User::find()
                        ->limit(12000)
                        ->orderBy('id')
                    ;

                    if(isset($get['part_2'])){
                        $model->offset(12000);
                    }

                    $all_users = [];
                    foreach ($model->batch(30) as $users) {
                        if (is_array($users)) {
                            foreach ($users as $user) {
                                $all_users[$user->id] = $user;
                            }
                        }
                    }
                    $all_users = array_reverse($all_users, true);
                    User::exportAll($all_users, 'all_users');
                    \Yii::$app->end();

                }elseif(isset($get['export_users_oysters'])) {

//                    $sql = Orders::find()
//                            ->select(['orders.id', 'oi.id', 'oi.data'])
//                            ->where(['user_id' => 14713])
//                            ->rightJoin(['oi' => 'orders_items'], "oi.data LIKE '%устриц%'")
//                            ->andWhere([
//                                'orders.id' => OrdersItems::find()->select('oi.order_id')->where(['like', 'data', 'устриц'])
//                            ]);
//                    $sql->asArray();
//                    $items = $sql->all();
//                    d::pex($items);

                    $sql = User::find()
                        ->where([
                            'id' => Orders::find()->select('user_id')
                                ->where([
                                    'id' => OrdersItems::find()->select('order_id')->where(['like', 'data', 'устриц'])
                                ])
                                ->andWhere(['payment' => 2])
                                ->andWhere(['in', 'status', [4, 5]])
                                ->andFilterWhere([
                                    'or',
                                    ['like', 'pay_status', 'success'],
                                    ['like', 'pay_status', 'success_surcharge'],
                                ])
                        ]);
                    $all_users = $sql->all();

//                    d::pex($all_users);
//                    d::pex(count($all_users));

                    $all_users = array_reverse($all_users, true);
                    User::exportAll($all_users, 'all_users_oysters', 'export_users_oysters');
                    \Yii::$app->end();
                }

                return $this->render('admin-debug');
            }else{
                $response = 'Ничего не произошло';
                switch($post['type']){
                    case '':
                        break;
                    default:
                        $response = d::getDebug();
                }

                d::ajax($response);
            }
        }
    }
}
