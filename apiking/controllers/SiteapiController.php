<?php

namespace apiking\controllers;

use common\components\Debugger as d;
use apiking\form\FastOrder;
use common\models\Actions;
use common\models\ItemFavorites;
use yii\db\ActiveQuery;
use common\models\Cards;
use common\models\Category;
use common\models\Items;
use common\models\City;
use common\models\User;
use common\models\Brands;
use common\models\Reviews;
use common\models\ReviewsItem;
use common\models\Orders;
use common\models\ItemImg;
use common\models\Pickpoint;
use common\models\PickpointImg;
use apiking\models\Delivery;
use common\models\OrdersUnloading;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\web\Response;
use common\models\AboutHistory;
use backend\models\Pages;
use common\models\News;
use yii\web\Cookie;
use yii\data\Pagination;
use yii\web\BadRequestHttpException;
use yii\db\Expression;
use common\models\Recipes;
use common\models\Banners;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Session;

/**
 * Описание главного меню и меню товаров 'K I N G F I S H E R'
 * baseURL = http://kingfisher.kz
 *
 */
class SiteapiController extends MainController
{
	/**
	 * @ignore
	 */
	public $modelClass = ItemFavorites::class;

	/**
	 * @ignore
	 */
	public $enable_discount = true;

	/**
	 * @ignore
	 */
	protected function verbs()
	{
		return [
			'abouthistory' => ['POST', 'GET'],
			'actionPaymentdelivery' => ['GET', 'HEAD'],
			'news' => ['GET', 'HEAD', 'POST'],
			'page' => ['GET'],
			'сontacts' => ['GET'],
			'сatalog' => ['GET'],
			'headcatalog' => ['GET'],
			'item' => ['GET'],
			'itemm' => ['GET'],
			'recipes' => ['GET'],
			'recipe' => ['GET'],
			'actions' => ['GET'],
			'delivery' => ['GET'],
			'getcities' => ['GET'],
			'getbanners' => ['GET'],
			'search' => ['GET'],
			'fastorder' => ['POST'],
			'ordersunloading' => ['POST'],
			'products' => ['GET'],
			'reviews' => ['GET'],
			'reviewsitem' => ['GET'],
			'assessmentyandex' => ['POST'],
			'reviewitemsend' => ['POST'],
			'brandsonecategory' => ['GET'],
			'index' => ['GET'],
			'getpickpoints' => ['GET'],
			'hitsalenew' => ['GET'],
			'paymentorder' => ['POST'],
			'post3dorder' => ['POST'],
			'getDebug' => ['GET'],
            'getTimeWork' => ['GET'],
		];
	}

	public function actionGetDebug()
	{

//        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$data = d::getDebug();
        if(d::isJson($data)){
            $data = json_decode($data);
        }
        return $data;
	}

	public function actionIndex()
	{
		$data = Pages::findOne(5);
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $data;
	}

	public function actionGetTimeWork()
    {
        $time_work = [
            [ 'maxTimeForDayToDay' => '11:00', 'startTime' => '14:00', 'endTime' => '22:00' ],
            [ 'maxTimeForDayToDay' => '11:00', 'startTime' => '14:00', 'endTime' => '22:00' ],
            [ 'maxTimeForDayToDay' => '11:00', 'startTime' => '14:00', 'endTime' => '22:00' ],
            [ 'maxTimeForDayToDay' => '11:00', 'startTime' => '14:00', 'endTime' => '22:00' ],
            [ 'maxTimeForDayToDay' => '11:00', 'startTime' => '14:00', 'endTime' => '22:00' ],
            [ 'maxTimeForDayToDay' => '11:00', 'startTime' => '14:00', 'endTime' => '22:00' ],
            null,
        ];
		return $time_work;
    }

	/**
	 * ОПЛАТА И ДОСТАВКА. Метод при нажатии на раздел "ОПЛАТА И ДОСТАВКА" верхнего меню
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/paymentdelivery  <br>
	 * Возвращается строка<br>
	 * {<br>
	 *&#8195;"id": 5, <br>
	 *&#8195;"name": "Оплата и доставка", <br>
	 *&#8195;"body": "текст с тегами" <br>
	 *&#8195;"isVisible": 1, <br>
	 *&#8195;"not_delete": 1 <br>
	 *}<br>
	 * @return array
	 */
	public function actionPaymentdelivery()
	{
		$data = Pages::findOne(5);
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $data;
	}

	/**
	 * О НАС (ИСТОРИЯ КОМПАНИИ). Метод при нажатии на раздел "О нас" верхнего меню
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/abouthistory  <br>
	 * Возвращается строка <br>
	 * {<br>
	 *&#8195;"id": 9,<br>
	 *&#8195;"year": "2014",<br>
	 *&#8195;"body": "Старт работы с частными клиентами. Открытие собстональных доставя. Победа и переход на другой уровень.",<br>
	 *&#8195;"sort": 9	<br>
	 * }<br>
	 * @return array
	 */
	public function actionAbouthistory()
	{
		$data['items'] = AboutHistory::find()->orderBy(['sort' => SORT_DESC])->all();
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $data;
	}

	/**
	 * НОВОСТИ (НОВОСТЬ). Метод при нажатии на раздел "НОВОСТИ" верхнего меню
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/news   - для получения списка новостей<br>
	 *  baseURL/apiking/siteapi/news?id=5  - для получения одной новости, где id - id новости<br>
	 * Возвращается - если выводится список новостей<br>	
	 *{<br>
	 *&#8195;"data": {<br>
	 *&#8195;"years": [<br>
	 *&#8195;&#8195;2016<br>
	 *&#8195;],<br>
	 *"select_year": "",<br>
	 *"main_year": 2016,<br>
	 *"items": [<br>
	 * {<br>
	 *"id": 5,<br>
	 *"name": "NEW from Canada !!!",<br>
	 *"small_body": "Компания KingFisher рада сообщить хорошую новость. ",<br>
	 *"body": "текст новости",<br>
	 *"img": "/uploads/news/5732af39acbda_img.jpg",
	 *"created_at": 1462924800,
	 *"isVisible": 1
	 *},
	 *],<br>
	 *"pages": {<br>
	 *"pageParam": "page",<br>
	 *"pageSizeParam": "per-page",<br>
	 *"forcePageParam": true,<br>
	 *"route": null,<br>
	 *"params": null,<br>
	 *"urlManager": null,<br>
	 *"validatePage": true,<br>
	 *"totalCount": "2",<br>
	 *"defaultPageSize": 20,<br>
	 *"pageSizeLimit": [<br>
	 * 1,<br>
	 *  50<br>
	 *],<br>
	 *}<br>
	 *},<br>
	 *"prePath": "первая часть пути к картинкам (из базы выводится вторая часть)"<br>
	 *}<br>
	 * Возвращается - если выводится одна новость<br>
	 * array<br>
	 *"id": 5,<br>
	 *"name": "NEW from Canada !!!",<br>
	 *"small_body": "Компания KingFisher рада сообщить хорошую новость. ",<br>
	 *"body": "текст с тегами" <br>
	 *"img": "/uploads/news/5732af39acbda_img.jpg",<br>
	 *"created_at": 1462924800,<br>
	 *"isVisible": 1<br>
	 * @return array
	 */
	public function actionNews()
	{
		$prePath = Yii::$app->function_system->getPrePathPictures();

		if ($id = Yii::$app->request->get('id')) {
			if ($item = News::find()->andWhere(['id' => intval($id), 'isVisible' => 1])->one()) {
				$news = News::find()
					->andWhere(['<>', 'id', $item->id])
					->andWhere(['isVisible' => 1])
					->andWhere(['>=', 'created_at', $item->created_at - 2629743])
					->andWhere(['<=', 'created_at', $item->created_at + 2629743])
					->limit(4)
					->all();
				return [
					'item' => $item,
					'prePath' => $prePath
				];
			} else {
				throw new BadRequestHttpException('Данная новость не найдена');
			}
		} else {
			$data = [
				'years' => [],
				'select_year' => ''
			];
			$select_year = Yii::$app->request->get('year');
			$years = Yii::$app->cache->get('news_years');
			if (!$years) {
				$years = [];
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

			return [
				'data' => $data,
				'prePath' => $prePath
			];
		}
	}

	/**
	 * ОПТОВИКАМ. Метод при нажатии на раздел "ОПТОВИКАМ" верхнего меню
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/page?id=1   - получение страницы оптовикам, где id постоянный параметр<br>
	 * Возвращается<br>
	 * array<br>
	 *"id": 1,<br>
	 *"name": "Оптовикам",<br>
	 *"body": "текст с тегами",<br>
	 *"isVisible": 1,<br>
	 *"not_delete": 0<br>
	 * @return array
	 */
	public function actionPage($id)
	{
		$item = Pages::find()->andWhere(['isVisible' => 1, 'id' => intval($id)])->one();
		if ($item) {
			$data['item'] = $item;
			return $data;
		} else {
			throw new BadRequestHttpException();
		}
	}

	/**
	 * КОНТАКТЫ. Метод при нажатии на раздел "КОНТАКТЫ" верхнего меню
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/contacts <br>
	 * @return string
	 */
	public function actionContacts()
	{
		$context = \Yii::$app->settings->get('contact_text');
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $context;
	}

	/**
	 * ДОСТАВКА В ГОРОДЕ (ВНИЗУ РАЗДЕЛА ОПЛАТА И ДОСТАВКА НА САЙТЕ). Метод при нажатии на раздел "ОПЛАТА И ДОСТАВКА" верхнего меню
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/delivery?id=5, где id - id города<br> 
	 * Возвращается строка<br>
	 * {<br>
	 *&#8195;"id": 1,<br>
	 *&#8195;"name": "Алматы",<br>
	 *&#8195;"phone": "+7 (727) 341 03 11",<br>
	 *&#8195;"price_delivery": 123,<br>
	 *&#8195;"pickup": "Алматы, ул. Айманова 155, Торговый Дом &quot;Дархан&quot;, магазин &quot;Кингфишер&quot;<br /> &nbsp;",<br>
	 *&#8195;"info_delivery": "Телефоны для заказа:",<br>
	 *&#8195;"isOnlyPickup": 0,<br>
	 *&#8195;"not_delete": 1,<br>
	 *&#8195;"payment_type": [<br>
	 *&#8195;&#8195;"payment_type_online",<br>
	 *&#8195;&#8195;"payment_type_cards"<br>
	 *&#8195;],<br>
	 *&#8195;"delivery_weight_sum": 456,<br>
	 *&#8195;"delivery_free_sum": 10000<br>
	 * @return string
	 */
	public function actionDelivery($id)
	{
		$item = City::find()->andWhere(['id' => intval($id)])->one();
		if ($item) {
			$data['item'] = $item;
			return $data;
		} else {
			throw new BadRequestHttpException();
		}
	}

	/**
	 * СПИСОК РЕЦЕПТОВ. Метод при нажатии на раздел "РЕЦЕПТЫ" в футере сайта
	 *
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/recipes <br>
	 * Возвращается<br>
	 *		{<br>
	 *	"data": {<br>
	 *	"items": [<br>
	 *	  {<br>
	 *	"id": 21,<br>
	 *	"name": "Филе групера с чечевицей",<br>
	 *	"time_cooking": "25 минут",<br>
	 *	"small_body": "Очень просто и необычно.",<br>
	 *	"img_list": "/uploads/recipes/5ab4cf44983c5_img_list.jpg",<br>
	 *	"isVisible": 1,<br>
	 *	"isDay": 0,<br>
	 *	"created_at": 1521798980,<br>
	 *	"updated_at": 1521799596<br>
	 *	},<br>
	 *	],<br>
	 *	"hasPage": false,<br>
	 *	"pages": {<br>
	 *	"pageParam": "page",<br>
	 *	"pageSizeParam": "per-page",<br>
	 *	"forcePageParam": true,<br>
	 *	"route": null,<br>
	 *	"params": null,<br>
	 *	"urlManager": null,<br>
	 *	"validatePage": true,<br>
	 *	"totalCount": "12",<br>
	 *	"defaultPageSize": 20,<br>
	 *	"pageSizeLimit": [<br>
	 *	  1,<br>
	 *	  50<br>
	 *	],<br>
	 *	}<br>
	 *	},<br>
	 *	"prePath": "первая часть пути к картинкам (из базы выводится вторая часть)"<br>
	 *	}<br>
	 * @return array
	 */
	public function actionRecipes()
	{
		$prePath = Yii::$app->function_system->getPrePathPictures();

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
			return [
				'data' => $data,
				'prePath' => $prePath
			];
		} else {
			return [
				'data' => $data,
				'prePath' => $prePath
			];
		}
	}

	/**
	 * ОДИН РЕЦЕПТ ИЗ СПИСКА РЕЦЕПТОВ. Метод при нажатии на один рецепт из раздела "РЕЦЕПТЫ" в футере сайта
	 *
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/recipe?id=$id, где id это id рецепта из 'СПИСОК РЕЦЕПТОВ'<br>
	 * Возвращается<br>
	 * string <br>
	 *	"data": {<br>
	 *	"item": {<br>
	 *	"id": 3,<br>
	 *	"name": "Похлебка по сицилийски из Сибаса",<br>
	 *	"time_cooking": "20 минут",<br>
	 *	"small_body": "Сибас, запеченный в фольге испанская кухня",<br>
	 *	"img_list": "/uploads/recipes/3_img_list.jpg",<br>
	 *	"isVisible": 1,<br>
	 *	"isDay": 1,<br>
	 *	"created_at": 1446215614,<br>
	 *	"updated_at": 1463449807<br>
	 *	}<br>
	 *	},<br>
	 *	"prePath": "первая часть пути к картинкам (из базы выводится вторая часть)"<br>
	 *	}<br>	
	 * @return array
	 */
	public function actionRecipe($id)
	{
		$prePath = Yii::$app->function_system->getPrePathPictures();

		$item = Recipes::findOne(['id' => intval($id), 'isVisible' => 1]);

		$ingredients = $item->getRecipesItems()->with('item')->all();


		$methods = $item->getRecipesMethods()->orderBy(['sort' => SORT_ASC])->all();

		$goods = $item->getItems()->andWhere(['isVisible' => 1])->all();

        $goods_ = [];
        if($goods AND count($goods) > 0){
            foreach($goods as $good_k => $good){
                // Активация оптовой цены и персональной скидки на товар
                $good->price = $good->real_price();
                $goods_[$good_k] = $good;
            }
        }

		$imgs = $item->recipesImgs;

		if ($item) {
			$data['item'] = $item;
			return [
				'data' => $data,
				'ingredients' => $ingredients,
				'methods' => $methods,
				'use_recipes' => $goods_,
				'imgs' => $imgs,
				'share' => "https://" . $_SERVER['SERVER_NAME'] . "/site/recipe.html?id=$id",
				'prePath' => $prePath
			];
		} else {
			throw new BadRequestHttpException('Данный рецепт не найден');
		}
	}

	/**
	 * СПИСОК АКЦИЙ (ОДНА АКЦИЯ ЕСЛИ ПЕРЕДАЕТСЯ ID). Метод при нажатии на раздел "АКЦИИ" в футере сайта
	 *
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/actions<br>
	 *
	 * Если список акций, то возвращается строка <br>	
	 *	{	<br>	
	 *	"data": {<br>
	 *	"items": [<br>
	 *	  {<br>
	 *	"id": 4,<br>
	 *	"name": "Икра красная, Канада, 23990 тг/упаковка весом 1 кг.",<br>
	 *	"small_body": "Экопродукт, без консервантов.",<br>
	 *	"body": "сообщение акции",<br>
	 *	"img": "/uploads/actions/4_img.jpg",<br>
	 *	"created_at": 1529280000,<br>
	 *	"date_start": 1529280000,<br>
	 *	"date_end": 1529971200,<br>
	 *	"isVisible": 1<br>
	 *	},],<br>
	 *	"pages": {<br>
	 *	"pageParam": "page",<br>
	 *	"pageSizeParam": "per-page",<br>
	 *	"forcePageParam": true,<br>
	 *	"route": null,<br>
	 *	"params": null,<br>
	 *	"urlManager": null,<br>
	 *	"validatePage": true,<br>
	 *	"totalCount": "6",<br>
	 *	"defaultPageSize": 20,<br>
	 *	"pageSizeLimit": [<br>
	 *	  1,<br>
	 *	  50<br>
	 *	],<br>
	 *	}<br>
	 *	},<br>
	 *	"prePath": "первая часть пути к картинкам (из базы выводится вторая часть)"<br>
	 *	}<br>
	 *	
	 * Если одна акция <br>	
	 *	{
	 *	"item": {
	 *	"id": 45,
	 *	"name": "Кальмары молодые ",
	 *	"small_body": "цена 3490 тг/кг замороженные",
	 *	"body": "сообщение акции",<br>
	 *	"img": "/uploads/actions/5aa7479660623_img.jpg",
	 *	"created_at": 1529280000,
	 *	"date_start": 1529280000,
	 *	"date_end": 1529971200,
	 *	"isVisible": 1
	 *	},
	 *	"actions": [],
	 *	"prePath": "первая часть пути к картинкам (из базы выводится вторая часть)"<br>
	 *	}
	 * @return array
	 */
	public function actionActions()
	{
		$prePath = Yii::$app->function_system->getPrePathPictures();

		if ($id = Yii::$app->request->get('id')) {
			if ($item = Actions::find()->andWhere(['id' => intval($id), 'isVisible' => 1])->one()) {
				$actions = Actions::find()
					->andWhere(['<>', 'id', $item->id])
					->andWhere(['isVisible' => 1])
					->andWhere(['>=', 'date_end', time()])
					->limit(4)
					->all();

				$items_action = $item->getItems()->andWhere(['isVisible' => 1])->all();
				$share = "https://" . $_SERVER['SERVER_NAME'] . "/site/actions.html?id=$id";

				return ['item' => $item, 'actions' => $actions, 'prePath' => $prePath, 'items_action' => $items_action, 'share' => $share];
			} else {
				throw new BadRequestHttpException('Данная новость не найдена');
			}
		} else {
			$data = [];
			$q = Actions::find()->orderBy(['date_end' => SORT_DESC])->where(['isVisible' => 1]);
			if ($request_count = Yii::$app->request->get('count')) {
				if ($request_count == 'all') {
					$data['items'] = $q->all();
				} else {
					if (is_numeric($request_count)) {
						$count = $q->count();
						$pages = new Pagination(['totalCount' => $count]);
						$pages->setPageSize($request_count);
						$data['items'] = $q->offset($pages->offset)
							->limit($pages->limit)
							->all();
						$data['pages'] = $pages;
					} else {
						$data['items'] = $q->all();
					}
				}
			} else {
				$count = $q->count();
				$pages = new Pagination(['totalCount' => $count]);
				$pages->setPageSize(8);
				$data['items'] = $q->offset($pages->offset)
					->limit($pages->limit)
					->all();
				$data['pages'] = $pages;
			}
			return [
				'data' => $data,
				'prePath' => $prePath
			];
		}
	}

	/**
	 * ГЛАВНОЕ НАЧАЛЬНОЕ МЕНЮ КАТЕГОРИЙ. Метод при загрузке меню категорий
	 * https://www.figma.com/proto/Z9Cj64U9Y6omjBoNKrqd9t/kingfisher?node-id=102%3A3696&viewport=554%2C391%2C0.5&scaling=scale-down
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/headcatalog <br>
	 * Возвращается<br>
	 * array [
	 * {
	 *	"id": id категории,<br>
	 *	"name": "название категории",<br>
	 *	"isVisible": видимость(1),<br>
	 *	"isDeleted": удалена(0),<br>
	 *	"parent_id": наличие родителя(null),<br>
	 *	"sort": сортировка(5),<br>
	 *	"type": "тип",<br>
	 *	"slug": "транслитерация"<br>
	 *}
	 * ]<br>
	 * @return array
	 */
	public function actionHeadcatalog()
	{
		$prePath = Yii::$app->function_system->getPrePathPictures();
		$cat = Category::find()->where(['isVisible' => 1, 'parent_id' => NULL, 'isDeleted' => 0])->all();
		if ($cat) {
			$r_cat = [];
			foreach ($cat as $c) {
				$r_cat[$c->id] = $c->name . ' ' . $c->isWholesale;
			}

			//d::ajax($r_cat);

			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			return [
				'data' => $cat,
				'prePath' => $prePath
			];
		} else {
			throw new BadRequestHttpException('Категории отсутствуют');
		}
	}

	/**
	 * ПОДРОБНОЕ МЕНЮ КАТЕГОРИЙ
	 * (при нажатии на раздел меню должен срабатывать этот метод, id - это id из 'ГЛАВНОЕ НАЧАЛЬНОЕ МЕНЮ КАТЕГОРИЙ')
	 * https://www.figma.com/proto/Z9Cj64U9Y6omjBoNKrqd9t/kingfisher?node-id=102%3A3696&viewport=554%2C391%2C0.5&scaling=scale-down
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/catalog?id=id<br>
	 * Возвращается строка<br>
	 *	{<br>
	 *	"data": {<br>
	 *	"cats"(массив всех категорий этого раздела (внимание не абсолютно всех, а только раздела), для хлебных крошек): {<br>
	 *	10: {<br>
	 *	"id": 10,<br>
	 *	"name": "Гребешок",<br>
	 *	"isVisible": 1,<br>
	 *	"isDeleted": 0,<br>
	 *	"parent_id": 9,<br>
	 *	"sort": 6,<br>
	 *	"type": "items",<br>
	 *	"slug": "grebeshok"<br>
	 *	},<br>
	 *	},<br>
	 *	"sub_cats": [],<br>
	 *	"cat": {<br>
	 *	"id": 9,<br>
	 *	"name": "Морепродукты",<br>
	 *	"isVisible": 1,<br>
	 *	"isDeleted": 0,<br>
	 *	"parent_id": null,<br>
	 *	"sort": 0,<br>
	 *	"type": "cats",<br>
	 *	"slug": "moreprodukty"<br>
	 *	},<br>
	 *	"sub_cat": false,<br>
	 *	"order": "popularity_desc",<br>
	 *	"model": {},<br>
	 *	"items": [<br>
	 *	  {<br>
	 *	"id": 51,<br>
	 *	"cid": 19,<br>
	 *	"brand_id": null,<br>
	 *	"article": "",<br>
	 *	"name": "Креветка Королевская 16/20, уп. 1 кг",<br>
	 *	"body": "",<br>
	 *	"body_small": "Свежемороженая, в панцире, без головы, производство Индия",<br>
	 *	"feature": "",<br>
	 *	"storage": "",<br>
	 *	"delivery": "",<br>
	 *	"discount": null,<br>
	 *	"bonus_manager": 1,<br>
	 *	"price"(цена за штуку): 4900,<br>
	 *	"old_price": null,<br>
	 *	"purch_price": 3000,<br>
	 *	"wholesale_price": 3900,<br>
	 *	"count": 10000,<br>
	 *	"isVisible": 1,<br>
	 *	"isWholesale": 0,<br>
	 *	"video": "",<br>
	 *	"img_list": "/uploads/items/56f5192271bcb_img_list.jpg",<br>
	 *	"isHit": 1,<br>
	 *	"isNew": 0,<br>
	 *	"measure": 1,<br>
	 *	"measure_price": 1,<br>
	 *	"weight": 1,<br>
	 *	"popularity": null,<br>
	 *	"slug": "krevetka-korolevskaya-16-20-up-1-kg"<br>
	 *	}<br>
	 *	],<br>
	 *	},<br>
	 *	"prePath": "первая часть пути к картинкам (из базы выводится вторая часть)"<br>
	 *	}<br>			
	 * @return array
	 */
	public function actionCatalog($id)
	{
		$prePath = Yii::$app->function_system->getPrePathPictures();

		$cat = Category::find()->where(['isVisible' => 1, 'id' => intval($id)])->one();

		if ($cat) {
			$data = [
				'cats' => [],
				'sub_cats' => [],
				'cat' => $cat,
				'sub_cat' => false
			];
			$q = new ActiveQuery(Items::className());
			$all_parents = $cat->allParents();
			$count_parents = count($all_parents);
			//     if ($cat->type == 'cats' && $count_parents == 0) {
			if ($cat->type == 'cats') {
				$cats = $cat->getCategories()->where(['isVisible' => 1])->orderBy(['sort' => SORT_ASC])->indexBy('id')->asArray()->all();
				$cats_ = [];
				foreach ($cats as $key => $result) {
					$count_parent = Category::find()->where(['isVisible' => 1, 'parent_id' => intval($result['id'])])->count();
					$result['hasChildren'] = ($count_parent > 0 ? true : false);
					$cats_[] = $result;
				}
				/*
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
				*/
			} else {
				/*
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
				])->orderBy(['sort' => SORT_ASC])->indexBy('id')->asArray()->all();
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
				*/
			}


			/*
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
			*/
			//   $data['model'] = new Items();
			//   $items = $q->all();
			//   $data['items'] = $items;
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			return [
				'data' => $cats_,
				'prePath' => $prePath
			];
		} else {
			throw new BadRequestHttpException('Данная категория не найдена');
		}
	}

	/**
	 * ПОЛУЧИТЬ ТОВАРЫ ОДНОЙ КАТЕГОРИИ (ДОПОЛНИТЕЛЬНЫЙ) С СОРТИРОВКОЙ
	 * (при нажатии на раздел меню должен срабатывать этот метод, id - это id из 'ГЛАВНОЕ НАЧАЛЬНОЕ МЕНЮ КАТЕГОРИЙ')
	 * https://www.figma.com/proto/Z9Cj64U9Y6omjBoNKrqd9t/kingfisher?node-id=102%3A3696&viewport=554%2C391%2C0.5&scaling=scale-down
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/products<br>
	 * передать {"id":"id категории",  "order" : "price_asc(если цена по возрастатнию), price_desc(если цена по убыванию), name_asc, name_desc, popularity_desc",
	 * "offset":"смещение(по умолчанию 0)", "limit":"количество за раз(по умолчанию 10)", "brands":"строка с перечислениями id брендов"}
	 * Возвращается строка<br>
	 *	{<br>
	 *	"data": {<br>
	 *	"cats"(массив всех категорий этого раздела (внимание не абсолютно всех, а только раздела), для хлебных крошек): {<br>
	 *	10: {<br>
	 *	"id": 10,<br>
	 *	"name": "Гребешок",<br>
	 *	"isVisible": 1,<br>
	 *	"isDeleted": 0,<br>
	 *	"parent_id": 9,<br>
	 *	"sort": 6,<br>
	 *	"type": "items",<br>
	 *	"slug": "grebeshok"<br>
	 *	},<br>
	 *	},<br>
	 *	"sub_cats": [],<br>
	 *	"cat": {<br>
	 *	"id": 9,<br>
	 *	"name": "Морепродукты",<br>
	 *	"isVisible": 1,<br>
	 *	"isDeleted": 0,<br>
	 *	"parent_id": null,<br>
	 *	"sort": 0,<br>
	 *	"type": "cats",<br>
	 *	"slug": "moreprodukty"<br>
	 *	},<br>
	 *	"sub_cat": false,<br>
	 *	"order": "popularity_desc",<br>
	 *	"model": {},<br>
	 *	"items": [<br>
	 *	  {<br>
	 *	"id": 51,<br>
	 *	"cid": 19,<br>
	 *	"brand_id": null,<br>
	 *	"article": "",<br>
	 *	"name": "Креветка Королевская 16/20, уп. 1 кг",<br>
	 *	"body": "",<br>
	 *	"body_small": "Свежемороженая, в панцире, без головы, производство Индия",<br>
	 *	"feature": "",<br>
	 *	"storage": "",<br>
	 *	"delivery": "",<br>
	 *	"discount": null,<br>
	 *	"bonus_manager": 1,<br>
	 *	"price"(цена за штуку): 4900,<br>
	 *	"old_price": null,<br>
	 *	"purch_price": 3000,<br>
	 *	"wholesale_price": 3900,<br>
	 *	"count": 10000,<br>
	 *	"isVisible": 1,<br>
	 *	"isWholesale": 0,<br>
	 *	"video": "",<br>
	 *	"img_list": "/uploads/items/56f5192271bcb_img_list.jpg",<br>
	 *	"isHit": 1,<br>
	 *	"isNew": 0,<br>
	 *	"measure": 1,<br>
	 *	"measure_price": 1,<br>
	 *	"weight": 1,<br>
	 *	"popularity": null,<br>
	 *	"slug": "krevetka-korolevskaya-16-20-up-1-kg"<br>
	 *	}<br>
	 *	],<br>
	 *	},<br>
	 *	"prePath": "первая часть пути к картинкам (из базы выводится вторая часть)"<br>
	 *	}<br>			
	 * @return array
	 */
	public function actionProducts($id, $order, $offset = 0, $limit = 10, $brands = '')
	{

		$prePath = Yii::$app->function_system->getPrePathPictures();

		$cat = Category::find()->where(['isVisible' => 1, 'id' => intval($id)])->one();

		if ($cat) {
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
				$cats = $cat->getCategories()->where(['isVisible' => 1])->orderBy(['sort' => SORT_ASC])->indexBy('id')->asArray()->all();
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

			$brands_ = [];

			if ($brands != '') {
				$brands_ = explode(',', $brands);
			}

			$q->andWhere([
				'`items`.isVisible' => 1,
			]);
			$q->andWhere([
				'OR',
				['`items`.cid' => $cats_a],
				['`items_category`.category_id' => $cats_a]
			]);
			$q->groupBy(['`items`.id']);

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

			//		$count = $q->count();
			$q->limit($limit)->offset($offset);
			$items = $q->all();
			$items_ = [];

			$count = 0;

			if (!empty($brands_)) {

				foreach ($items as $key => $result) {
                    $result->price = $result->real_price();
					if (!empty($brands_) && (in_array($result->brand_id, $brands_))) {

						foreach ($result as $k => $r) {

							$result_[$k] = $r;

							$itemImg = ItemImg::find()
								->andWhere(['item_id' => $result->id])
								->all();

							$result_['itemImg'] = $itemImg;

							if ($result->img_list == '' AND isset($itemImg[0])) {
								$result_['img_list'] = $itemImg[0]['url'];
							}

						}
						$items_[] = $result_;
						$count = count($items_);
					}
				}
			} else {

				foreach ($items as $key => $result) {
                    $result->price = $result->real_price();
					foreach ($result as $k => $r) {

						$result_[$k] = $r;

						$itemImg = ItemImg::find()
							->andWhere(['item_id' => $result->id])
							->all();

						$result_['itemImg'] = $itemImg;

						if ($result->img_list == '') {
							$result_['img_list'] = (isset($itemImg[0]['url']) ? $itemImg[0]['url'] : '');
						}
					}
					$items_[] = $result_;
					$count = count($items_);
				}
			}

			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			return [
				'count' => $count,
				'data' => $items_,
				'prePath' => $prePath
			];
		} else {
			throw new BadRequestHttpException('Данная категория не найдена');
		}
	}

	/**
	 * ПОЛУЧИТЬ НОВИНКИ ХИТЫ АКЦИИ У ТОВАРОВ
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/hitsalenew?type=sale<br>
	 * передать {"type":"hit(хиты) или new(новинки) или sale(акции)", 
	 * "offset":"смещение(по умолчанию 0)", "limit":"количество за раз(по умолчанию 10)"}
	 * @return array
	 */
	public function actionHitsalenew($type, $offset = 0, $limit = 10)
	{
		$get = Yii::$app->request->get();

		$prePath = Yii::$app->function_system->getPrePathPictures();

		$q = new ActiveQuery(Items::className());

		$q->join('LEFT OUTER JOIN', '`items_category`', '`items_category`.`item_id`=`items`.`id`');

		$q->andWhere([
			'`items`.isVisible' => 1,
		]);

		if ($type == 'hit') {
			$q->andWhere([
				'`items`.isHit' => 1,
			]);
		}

		if ($type == 'sale') {
			$q->andWhere(['or', ['is not', 'old_price', null], ['is not', 'discount', null]]);
		}

		if ($type == 'new') {
			$q->andWhere([
				'`items`.isNew' => 1,
			]);
		}

		$q->groupBy(['`items`.id']);

		$q->limit($limit)->offset($offset);
		$items = $q->all();
		$items_ = [];

		$count = 0;
		$d = [];
		foreach ($items as $key => $result) {
			$result_ = [];
			$result->price = $result->real_price();
			foreach ($result as $k => $r) {

				$result_[$k] = $r;

				$itemImg = ItemImg::find()
					->andWhere(['item_id' => $result->id])
					->all();

				$result_['itemImg'] = $itemImg;
				// --
				if ($result->img_list == '' and isset($itemImg[0])) {
					$result_['img_list'] = $itemImg[0]['url'];
				}
			}
			if ($result->id == 4) {
				//				$d[] = [
//					'user_id' => Yii::$app->user->id,
//					'user_is_wholesale' => Yii::$app->user->identity->isWholesale,
//					'id' => $result->id,
//					'name' => $result->name,
//					'price' => $result->price
//				];
			}
			$result_['price'] = $result->price;
			$items_[] = $result_;
			$count = count($items_);
		}

		if (isset($get['test'])) {
			return $d;
		}

		return [
			'count' => $count,
			'data' => $items_,
			'prePath' => $prePath
		];
	}

	/**
	 * ПОЛУЧИТЬ БРЕНДЫ ОДНОЙ КАТЕГОРИИ
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/brandsonecategory<br>
	 * передать {"id":"id категории"}
	 * Возвращается строка<br>
	 *	"prePath": "первая часть пути к картинкам (из базы выводится вторая часть)"<br>
	 *	}<br>			
	 * @return array
	 */
	public function actionBrandsonecategory($id)
	{

		$prePath = Yii::$app->function_system->getPrePathPictures();

		$cat = Category::find()->where(['isVisible' => 1, 'id' => intval($id)])->one();

		if ($cat) {
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
				$cats = $cat->getCategories()->where(['isVisible' => 1])->orderBy(['sort' => SORT_ASC])->indexBy('id')->asArray()->all();
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


			if (isset($order)) {
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
			}

			$items = $q->all();

			$brand_ = [];

			$items_test = [];

			foreach ($items as $key => $result) {

				if (!empty($result->brand_id)) {

					if (!in_array($result->brand_id, $items_test)) {
						$items_test[] = $result->brand_id;
						$brands = Brands::findOne($result->brand_id);
						$brand_[] = $brands;
					}
				}
			}
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			return [
				'brands' => $brand_,
				'prePath' => $prePath
			];
		} else {
			throw new BadRequestHttpException('Данная категория не найдена');
		}
	}

	/**
	 * ПОДРОБНЕЕ ОБ ОДНОМ ТОВАРЕ
	 * (при нажатии на товар в списке должен срабатывать этот метод, id - это id товара из 'ПОДРОБНОЕ МЕНЮ КАТЕГОРИЙ')
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/item?id=id&city_id=$id<br>
	 *, где $id - id города, который выбирается, $city_id -id города<br>
	 * Возвращается строка<br>
	 * {<br>
	 * &#8195;   "data": {<br>
	 * &#8195;&#8195;	"item": {<br>
	 * 	&#8195;&#8195; "id": id товарв,<br>
	 * &#8195;&#8195;	"cid": 10,<br>
	 * &#8195;&#8195;"brand_id": null,<br>
	 * &#8195;&#8195;	"article": "",<br>
	 *&#8195;&#8195;	"name": "название товара",<br>
	 * &#8195;&#8195;		"body": "123",<br>
	 *&#8195;&#8195;	"body_small": описание товара,<br>
	 *&#8195;&#8195;		 "feature": "123",<br>
	 *&#8195;&#8195; 		"storage": "",<br>
	 * &#8195;&#8195;		"delivery": "",<br>
	 * &#8195;&#8195;		"discount": null,<br>
	 * &#8195;&#8195;		"bonus_manager": 0,<br>
	 *&#8195;&#8195;	"price": цена за штуку,<br>
	 * &#8195;&#8195;		"old_price": null,<br>
	 * &#8195;&#8195;		"purch_price": 2500,<br>
	 * &#8195;&#8195;		"wholesale_price": 300,<br>
	 * &#8195;&#8195;		"count": 200,<br>
	 * &#8195;&#8195;		"isVisible": 1,<br>
	 * &#8195;&#8195;		"isWholesale": 0,<br>
	 * &#8195;&#8195;		"video": "",<br>
	 *&#8195;&#8195;	"img_list": путь до картинки,<br>
	 * &#8195;&#8195;		"isHit": 0,<br>
	 * &#8195;&#8195;		"isNew": 0,<br>
	 * &#8195;&#8195;		"measure": 1,<br>
	 * 	&#8195;&#8195;	"measure_price": 1,<br>
	 * &#8195;&#8195;		"weight": 1,<br>
	 * &#8195;&#8195;		"popularity": 0,<br>
	 * &#8195;&#8195;		"slug": null<br>
	 * &#8195;		},<br>
	 * &#8195;		"recipes": [],<br>
	 * 	&#8195;	"associated(массив данных по рекомендованным товарам, которые расположены внизу на сайте в графе 'Рекомендуем',)": [<br>
	 * &#8195;  "id": id товарв,<br>
	 *&#8195;	"name": "название товара",<br>
	 *	&#8195;"body_small": описание товара,<br>
	 *	&#8195;"img_list": путь до картинки,<br>
	 *&#8195;	"price": цена за штуку,<br>
	 *&#8195;],<br>
	 *&#8195; "delivery": "<p>Бесплатная доставка при заказе от <b>10 000 тг.</b>, самовывоз бесплатно</p>"<br>
	 *&#8195; 		},<br>
	 * &#8195;		"prePath": "первая часть пути к картинкам (из базы выводится вторая часть)"<br>
	 * 		}	<br>		
	 * @return array
	 */
	public function actionItem($id, $city_id)
	{
		$get = Yii::$app->request->get();
		$prePath = Yii::$app->function_system->getPrePathPictures();

		$item = Items::findOne(['id' => intval($id), 'isVisible' => 1]);
		if ($item) {
			$cat = $item->c;
			$item->price = $item->real_price();
			$data['item'] = $item;
			$data['recipes'] = Recipes::find()
				->joinWith(['recipesItems'])
				->andWhere(['recipes_item.item_id' => $item->id])
				->limit(7)
				->all();
			$data['itemImg'] = ItemImg::find()
				->andWhere(['item_id' => $item->id])
				->all();
			$data['count_reviews'] = ReviewsItem::find()
				->andWhere(['item_id' => $id])
				->andWhere(['isVisible' => 1])
				->orderBy(['created_at' => SORT_DESC])->count();

			$associated = Items::find()
				->select('items.*')
				->join('INNER JOIN', 'item_associated', 'item_id_main=items.id OR item_id_sub=items.id')
				->andWhere(['<>', 'items.id', $item->id])
				->andWhere(['items.isVisible' => 1, 'item_associated.item_id_main' => $item->id])
				->limit(4)
				->all();

			$associated_ = [];

			foreach ($associated as $key => $result) {

                // Для применения персональных скидок
                $result->price = $result->real_price();

				foreach ($result as $k => $r) {

					$result_[$k] = $r;

					$itemImg = ItemImg::find()
						->andWhere(['item_id' => $result->id])
						->all();

					$result_['itemImg'] = $itemImg;

					if ($result->img_list == '' AND isset($itemImg[0])) {
						$result_['img_list'] = $itemImg[0]['url'];
					}

				}
				$associated_[] = $result_;
			}

			$data['associated'] = $associated_;



			//	$city = Yii::$app->request->cookies->getValue('city_select', 1);	
			//	$citys = Yii::$app->function_system->getCity_all();

			$city = $city_id;

			//	if (!isset($citys[$city])) {
			//		$city = 1;
			//	}

			if (!empty($city) && (int) $city > 0) {
				$data['delivery'] = (new Delivery())->getDeliveryTextInItem((int) $city);
			} else {
				$data['deliveryFree'] = '';
			}

//			d::tdfa('haha');

			return [
				'data' => $data,
				'prePath' => $prePath,
				'share' => "https://" . $_SERVER['SERVER_NAME'] . "/site/item.html?id=$id"
			];
		} else {
			throw new BadRequestHttpException('Данный товар не найден');
		}
	}



	// public function actionItemm($id)
	// { 	
	// $prePath = Yii::$app->function_system->getPrePathPictures();
// //	$url = Url::toRoute(['@web/site/item', 'id' => 3]);
// $url = 'https://' . $_SERVER['SERVER_NAME'] . '/site/item.html?id=3';
	// return [
	// 'data' => $url,
	// 'prePath' => $prePath
	// ];

	// $item = Items::findOne(['id' => intval($id), 'isVisible' => 1]);
	// if ($item) {
	// $cat = $item->c;                   
	// $data['item'] = $item;
	// $data['recipes'] = Recipes::find()
	// ->joinWith(['recipesItems'])
	// ->andWhere(['recipes_item.item_id' => $item->id])
	// ->limit(7)
	// ->all();
	// $data['itemImg'] = ItemImg::find()
	// ->andWhere(['item_id' => $item->id])
	// ->all();
	// $data['count_reviews'] = ReviewsItem::find()
	// ->andWhere(['item_id'=>$id])
	// ->andWhere(['isVisible' => 1])
	// ->orderBy(['created_at' => SORT_DESC])->count();

	// $data['associated'] = Items::find()
	// ->select('items.*')
	// ->join('INNER JOIN', 'item_associated', 'item_id_main=items.id OR item_id_sub=items.id')
	// ->andWhere(['<>', 'items.id', $item->id])
	// ->andWhere(['items.isVisible' => 1,'item_associated.item_id_main' => $item->id])
	// ->limit(4)
	// ->all();


	// //	$city = Yii::$app->request->cookies->getValue('city_select', 1);	
	// //	$citys = Yii::$app->function_system->getCity_all();

	// $city = 1;

	// //	if (!isset($citys[$city])) {
	// //		$city = 1;
	// //	}

	// if (!empty($city) && (int)$city > 0) {
	// $data['delivery'] = (new Delivery())->getDeliveryTextInItem((int)$city); 
	// }
	// else {
	// $data['deliveryFree'] = '';
	// }	

	// return [
	// 'data' => $data,
	// 'prePath' => $prePath
	// ];
	// } else {
	// throw new BadRequestHttpException('Данный товар не найден');
	// }
	// }


	/**
	 *  ПОИСК
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/search?query=слова для поиска<br>
	 * Возвращается строка<br>
	 * 		{<br>
	 *    "data": {<br>
	 *	"items":[<br>
	 * "id": 58,<br>
	 *"cid": 11,<br>
	 *"brand_id": null,<br>
	 *"article": "",<br>
	 *"name": "Мясо Мидий очищенное",<br>
	 *"body": "",<br>
	 *"body_small": "Размер, бланшированные, размер 200-300, пр-во Чили. ",<br>
	 *"feature": "",<br>
	 *"storage": "",<br>
	 *"delivery": "",<br>
	 *"discount": null,<br>
	 *"bonus_manager": 1,<br>
	 *"price": 1990,<br>
	 *"old_price": null,<br>
	 *"purch_price": 1500,<br>
	 *"wholesale_price": 1890,<br>
	 *"count": 10,<br>
	 *"isVisible": 1,<br>
	 *"isWholesale": 0,<br>
	 *"video": "",<br>
	 *"img_list": "/uploads/items/56f9155950bfe_img_list.png",<br>
	 *"isHit": 0,<br>
	 *"isNew": 0,<br>
	 *"measure": 0,<br>
	 *"measure_price": 1,<br>
	 *"weight": 1,<br>
	 *"popularity": 0,<br>
	 *"slug": null
	 *   ],<br>
	 * 		},
	 * 		"prePath": "первая часть пути к картинкам (из базы выводится вторая часть)"
	 * 		}
	 * @return string
	 */
	public function actionSearch($query, $offset = 0, $limit = 10)
	{
		$prePath = Yii::$app->function_system->getPrePathPictures();

		$data = [
			'items' => [],
			'news' => [],
			'query' => $query
		];
		$q = new ActiveQuery(Items::className());
		$q->andWhere([
			'OR',
			['like', 'items.name', $query],
			//        ['like', 'items.body', $query],
			['like', '`category`.name', $query],
		]);
		$q->join('LEFT JOIN', 'category', '`items`.cid=`category`.id');
		$q->andWhere(['`items`.isVisible' => 1, 'category.isVisible' => 1]);


		$count = $q->count();
		$q->limit($limit)->offset($offset);


		$data_items = $q->all();

		$items_ = [];

		foreach ($data_items as $key => $result) {
            $result->price = $result->real_price();
			foreach ($result as $k => $r) {
				$result_[$k] = $r;

				$itemImg = ItemImg::find()
					->andWhere(['item_id' => $result->id])
					->all();

				$result_['itemImg'] = $itemImg;

				if ($result->img_list == '' AND isset($itemImg[0])) {
					$result_['img_list'] = $itemImg[0]['url'];
				}

			}
			$items_[] = $result_;
		}

		$data['items'] = $items_;

		$q = new ActiveQuery(News::className());
		$q->andWhere([
			'OR',
			['like', 'name', $query],
			['like', 'body', $query],
			['like', 'small_body', $query],
		]);
		$q->andWhere(['isVisible' => 1]);
		$data['news'] = $q->all();

		return [
			'count' => $count,
			'data' => $data,
			'prePath' => $prePath
		];
	}

	/**
	 * КНОПКА КУПИТЬ В 1 КЛИК НА КАЖДОЙ КАРТОЧКЕ ТОВАРА НА ГЛАВНОЙ
	 * 
	 * POST запрос 
	 * отправить json {<br>
	 * &#8195;"name":"имя лица, которое заказывает", <br>
	 *&#8195;&#8195; "phone":"телефон лица, которое заказывает",<br>
	 * "items":id товара,<br>
	 * &#8195; "type":"1 - тип заказа, если вызывается метод с карточки товара, то это 1"<br>
	 *	} <br>
	 * header 'Accept:application/json'<br>
	 * header 'Content-Type:application/json'<br>
	 * на адрес<br>
	 *  baseURL/apiking/siteapi/fastorder <br>
	 * Возвращается<br>
	 * Возвращается объект данных<br>
	 * "message": {<br>
	 *"success": "Ваш заказ успешно отпрален!"<br>
	 *}<br>
	 * @return array 
	 */
	public function actionFastorder()
	{
		$model = new FastOrder();
		$model->load(Yii::$app->request->bodyParams, '');
		if ($result = $model->send()) {
			return $result;
		} else {
			return $model;
		}
	}

	/**
	 * ПОЛУЧИТЬ ВСЕ ГОРОДА
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/getcities<br> 
	 * Возвращается массив полных данных о городах<br>
	 * {<br>
	 *&#8195;"id": 1,<br>
	 *&#8195;"name": "Алматы",<br>
	 *&#8195;"phone": "+7 (727) 341 03 11",<br>
	 *&#8195;"price_delivery": 123,<br>
	 *&#8195;"pickup": "Алматы, ул. Айманова 155, Торговый Дом &quot;Дархан&quot;, магазин &quot;Кингфишер&quot;<br /> &nbsp;",<br>
	 *&#8195;"info_delivery": "Телефоны для заказа:",<br>
	 *&#8195;"isOnlyPickup": 0,<br>
	 *&#8195;"not_delete": 1,<br>
	 *&#8195;"payment_type": [<br>
	 *&#8195;&#8195;"payment_type_online",<br>
	 *&#8195;&#8195;"payment_type_cards"<br>
	 *&#8195;],<br>
	 *&#8195;"delivery_weight_sum": 456,<br>
	 *&#8195;"delivery_free_sum": 10000<br>
	 * @return string
	 */
	public function actionGetcities()
	{
		$paymentdelivery = $this->paymentdelivery();

		$prePath = Yii::$app->function_system->getPrePathPictures();
		$cities = City::find()->asArray()->all();

		if (!empty($cities)) {
			$cities_ = [];

			foreach ($cities as $result) {
				$pickpoints = [];
				$pickpoints = Pickpoint::find()->andWhere(['city_id' => $result['id']])->andWhere(['active' => 1])->all();
				$pickpoints_ = [];
				$payment_type_ = [];

				if (isset($pickpoints)) {

					foreach ($pickpoints as $pick) {

						$pickpointImgs = PickpointImg::find()->andWhere(['pickpoint_id' => $pick['id']])->all();

						$pickpoints_ = $pick;

						$pickpoints_['images'] = $pickpointImgs;
					}
				}

				$payment_type = trim($result['payment_type'], '[]');
				$payment_type = explode(",", $payment_type);

				foreach ($payment_type as $result_) {
					$payment_type_[] = $paymentdelivery[trim($result_, '"')];
				}
				$result['payment_type'] = $payment_type_;
				$result['pickup_points'] = $pickpoints;
				$cities_[] = $result;
			}

			$data['data'] = $cities_;
			$data['prePath'] = $prePath;
			return $data;
		} else {
			throw new BadRequestHttpException();
		}
	}

	/**
	 * ПОЛУЧИТЬ ВСЕ ПУНКТЫ САМОВЫВОЗА ОДНОГО ГОРОДА
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/getpickpoints?city_id=1<br>
	 *, $city_id -id города<br>	
	 * @return string
	 */
	public function actionGetpickpoints($city_id)
	{
		$pickpoints = [];
		$pickpoints = Pickpoint::find()->andWhere(['city_id' => $city_id])->all();
		return $pickpoints;
	}

	/**
	 * ПОЛУЧИТЬ ВСЕ БАННЕРЫ С ГЛАВНОЙ
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/getbanners<br> 
	 * Возвращается массив полных данных о городах<br>
	 * @return string
	 */
	public function actionGetbanners()
	{
		$prePath = Yii::$app->function_system->getPrePathPictures();
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$banners = Banners::find()->andWhere(['isVisible' => 1])->asArray()->all();

		$data = [];
		if (!empty($banners)) {
			$data['data'] = $banners;
			$data['prePath'] = $prePath;
			return $data;
		} else {
			throw new BadRequestHttpException();
		}
	}
	/**
	 * ПОЛУЧИТЬ АБСОЛЮТНО ВСЕ ОТЗЫВЫ
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/reviews<br> 
	 * Возвращается массив полных данных о городах<br>
	 * @return string
	 */
	public function actionReviews()
	{
		$data = [];
		$data['items'] = ReviewsItem::find()->orderBy(['created_at' => SORT_DESC])->where(['isVisible' => 1])->all();
		return $data;
	}

	/**
	 * ПОЛУЧИТЬ ВСЕ ОТЗЫВЫ ОДНОГО ТОВАРА
	 * 
	 * GET запрос на адрес<br>
	 *  baseURL/apiking/siteapi/reviewsitem?item_id=item_id<br> 
	 * Возвращается массив полных данных о городах<br>
	 * @return string
	 */
	public function actionReviewsitem($item_id)
	{
		$data = [];
		$reviews = ReviewsItem::find()->andWhere(['item_id' => $item_id])->andWhere(['isVisible' => 1])->orderBy(['created_at' => SORT_DESC])->all();
		$reviews_ = [];

		foreach ($reviews as $result) {
			$final = [];

			foreach ($result as $key => $res) {
				$final[$key] = $res;
			}
			$final['photo'] = '';

			if (!empty($result->user->photo)) {
				$final['photo'] = 'https://' . $_SERVER['HTTP_HOST'] . '/apiking/web/uploads/profile/' . $result->user->photo;
			}

			$reviews_[] = $final;
		}

		$data['reviews'] = $reviews_;

		return $data;
	}

	/**
	 * ОТПРАВИТЬ ОТЗЫВ ДЛЯ ОДНОГО ТОВАРА
	 * 
	 * POST запрос на адрес<br>
	 * строка запроса {"item_id":"id товара", "rate":"оценка","body":"сообщение","auth_token":"авторизационный токен"}<br>
	 *  baseURL/apiking/siteapi/reviewitemsend<br> 
	 * Возвращается массив<br>
	 * @return string
	 */
	public function actionReviewitemsend()
	{
		$auth_token = Yii::$app->request->post('auth_token');

		$user = User::findIdentityByAccessToken($auth_token);

		if (isset($user)) {

			$item_id = Yii::$app->request->post('item_id');
			$rate = Yii::$app->request->post('rate');
			$body = Yii::$app->request->post('body');
			$record = new ReviewsItem();

			$record->user_id = $user->id;
			$record->name = $user->username;

			$record->item_id = $item_id;
			$record->rate = $rate;
			$record->body = $body;

			$record->isVisible = 0;

			if ($record->save(false)) {
				$result_ = true;
			} else {
				$result_ = false;
			}
			$result['result'] = $result_;
			return $result;
		} else {
			return [
				'result' => 'no_authorization'
			];
		}
	}

	/**
	 * ПОКА НЕ РАБОТАЕТ
	 * первичная оцена ЯНДЕКС ДОСТАВКИ
	 * 
	 * POST запрос на адрес<br>
	 *  baseURL/apiking/siteapi/assessmentyandex<br> 
	 * передать джейсон 
	 * Возвращается массив полных данных о городах<br>
	 * @return string
	 */
	public function actionAssessmentyandex()
	{
		$longitude_from = \Yii::$app->request->post('longitude_from');
		$latitude_from = \Yii::$app->request->post('latitude_from');
		$longitude_to = \Yii::$app->request->post('longitude_to');
		$latitude_to = \Yii::$app->request->post('latitude_to');

		$params = array(
			'items' => array(
				array(
					"quantity" => 1,
					"size" => array(
						"height" => 0.1,
						"length" => 0.1,
						"width" => 0.1
					),
					"weight" => 2
				)
			),
			'requirements' => array(
				"taxi_class" => "express"
			),
			'route_points' => array(
				array(
					'coordinates' => array(
						(double) $longitude_from,
						(double) $latitude_from
					)
				),
				array(
					'coordinates' => array(
						(double) $longitude_to,
						(double) $latitude_to
					)
				)
			),
			"skip_door_to_door" => false

		);
		$string = json_encode($params);


		$string = json_encode($params);
		$ch = curl_init("https://b2b.taxi.yandex.net/b2b/cargo/integration/v1/check-price");
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			array(
				'Content-Type: application/json',
				'Accept-Language: en-US',
				'Authorization: Bearer AgAAAABI9dhGAAVM1TOtxQSy50o8kLMw_ZsZhTk',
				'Content-Length: ' . strlen($string)
			)
		);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $string);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$html = curl_exec($ch);
		curl_close($ch);
		$html = json_decode($html);


		\Yii::$app->response->format = Response::FORMAT_JSON;
		return [
			'code' => $html->price
		];
	}

	/**
	 * ВЫГРУЗКА ЗАКАЗОВ В 1C
	 * 
	 * POST запрос на адрес<br>
	 * baseURL/apiking/siteapi/ordersunloading<br>
	 * отправить {"secret":"секретный код"}<br>
	 * header 'Accept:application/json'<br>
	 * header 'Content-Type:application/json'<br>
	 * Возвращается<br>
	 * @return string
	 */
	public function actionOrdersunloading()
	{
		$info = Yii::$app->request->post();
		if ($info['secret'] == 'fkjskl12kd9sd3n3jdskjdsd') {

			$orders_unloading = OrdersUnloading::find()->join('LEFT JOIN', 'orders', '`orders`.id=`orders_unloading`.order_id')
				->join('LEFT JOIN', 'orders_items', '`orders_items`.order_id=`orders_unloading`.order_id')->all();

			$orders = [];

			foreach ($orders_unloading as $result) {
				$orders[$result['order_id']]['order'] = $result->orders;
				$orders[$result['order_id']]['items'] = $result->ordersItems;
			}

			$data['orders'] = $orders;
			//	OrdersUnloading::deleteAll();
			return $data;

		} else {
			return false;
		}
	}

	public function paymentdelivery()
	{

		$data = [
			"payment_type_cash" => [
				"id" => 1,
				"key" => "payment_type_cash",
				"name" => "Наличные"
			],
			"payment_type_online" => [
				"id" => 2,
				"key" => "payment_type_online",
				"name" => "Онлайн-оплата банковской картой"
			],
			"payment_type_cards" => [
				"id" => 3,
				"key" => "payment_type_cards",
				"name" => "Банковской картой при получении"
			],
			"payment_type_invoice" => [
				"id" => 4,
				"key" => "payment_type_invoice",
				"name" => "Счёт для оплаты"
			]
		];
		return $data;
	}

	/**
	 * ПЛАТЕЖ - 1 СТАДИЯ
	 * 
	 * POST запрос на адрес<br>
	 * baseURL/apiking/siteapi/paymentorder<br>
	 * отправить {"secret":"секретный код"}<br>
	 * header 'Accept:application/json'<br>
	 * header 'Content-Type:application/json'<br>
	 * Возвращается<br>
	 * @return string
	 */
	public function actionPaymentorder()
	{
		$remote_addr = $_SERVER['REMOTE_ADDR'];
		$order_id = \Yii::$app->request->post("order_id");
		$card_holder_name = \Yii::$app->request->post("card_holder_name");
		$order = Orders::findOne($order_id);

		$sum_real = $order->realSum();

		$data = json_encode(
			array(
				"Amount" => $sum_real,
				"Currency" => "KZT",
				"InvoiceId" => $order_id,
				"IpAddress" => $remote_addr,
				"Description" => "Оплата товаров в kingfisher.kz",
				"AccountId" => $order->user_id,
				"Name" => $card_holder_name,
				"CardCryptogramPacket" => \Yii::$app->request->post("card_cryptogram_packet"),
				"Payer" => array(
					"FirstName" => $order->user_name,
					"LastName" => "",
					"MiddleName" => "",
					"Birth" => "",
					"Address" => $order->user_address,
					"Street" => "",
					"City" => "",
					"Country" => "KZ",
					"Phone" => $order->user_phone,
					"Postcode" => ""
				)
			)
		);

		$username = \Yii::$app->params['cloudpayments']['public_id'];
		$password = \Yii::$app->params['cloudpayments']['api_key'];

		//	$ch = curl_init("https://api.cloudpayments.ru/payments/cards/charge");
		$ch = curl_init("https://api.cloudpayments.ru/payments/cards/auth");

		$headers = array(
			'Content-Type:application/json',
			//                    'Content-Length:' . strlen($data),
			'Authorization: Basic ' . base64_encode("$username:$password") // <---
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$html = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		$html_ = json_decode($html);

		if (!$html_->Success && $info['http_code'] == 200) {
			return $html_;
		}

		if ($html_->Success && $info['http_code'] == 200) {
			file_put_contents('1.txt', "\n - код  244444pay payment." . $html, FILE_APPEND | LOCK_EX);

			$cards_is_set = Cards::find()->andWhere(['card_last_four' => $html_->Model->CardLastFour, 'user_id' => $html_->Model->AccountId])->one();

			if (empty($cards_is_set)) {
				$cards = new Cards();
				$cards->name = (isset($html_->Model->Name) ? $html_->Model->Name : 'errore');
				$cards->token = (isset($html_->Model->Token) ? $html_->Model->Token : NULL);
				$cards->card_last_four = $html_->Model->CardLastFour;
				$cards->card_exp_date = $html_->Model->CardExpDate;
				$cards->card_type = $html_->Model->CardType;
				$cards->user_id = $html_->Model->AccountId;
				$cards->save();
			}
			return $html_;
		}

		if ($info['http_code'] == 400) {
			return $html_;
		}
	}

	/**
	 * ПРОВЕРКА 3D SECURE. ПОСЛЕ ПЛАТЕЖ
	 * 
	 * POST запрос на адрес<br>
	 * baseURL/apiking/siteapi/post3dorder<br>
	 * отправить {"transaction_id":"transaction_id", "pa_res":"pa_res"}<br>
	 * header 'Accept:application/json'<br>
	 * header 'Content-Type:application/json'<br>
	 * Возвращается<br>
	 * @return string
	 */
	public function actionPost3dorder()
	{
		//	$order_id = \Yii::$app->request->post("order_id");

		//	$order = Orders::findOne($order_id);

		$data = json_encode(
			array(
				"TransactionId" => \Yii::$app->request->post("transaction_id"),
				"PaRes" => \Yii::$app->request->post("pa_res")
			)
		);

		$username = \Yii::$app->params['cloudpayments']['public_id'];

		$password = \Yii::$app->params['cloudpayments']['api_key'];

		$ch = curl_init("https://api.cloudpayments.ru/payments/cards/post3ds");

		$headers = array(
			'Content-Type:application/json',
			'Content-Length:' . strlen($data),

			'Authorization: Basic ' . base64_encode("$username:$password") // <---
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$html = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		$html_ = json_decode($html);

		if (!$html_->Success && $info['http_code'] == 200) {
			file_put_contents('1.txt', "\n - 1код  pay payment." . $html, FILE_APPEND | LOCK_EX);

			// $cards_is_set = Cards::find()->andWhere(['card_last_four' => $html_->Model->CardLastFour, 'user_id' =>  $html_->Model->AccountId])->one();	

			// if (empty($cards_is_set)) {
			// $cards = new Cards();
			// $cards->name = (isset($html_->Model->Name) ? $html_->Model->Name : NULL);
			// $cards->token = (isset($html_->Model->Token) ? $html_->Model->Token : 'test');
			// $cards->card_last_four = $html_->Model->CardLastFour;
			// $cards->card_exp_date = $html_->Model->CardExpDate;
			// $cards->card_type = $html_->Model->CardType;
			// $cards->user_id = $html_->Model->AccountId;
			// $cards->save();
			// }	

			return $html_;
		}

		if ($html_->Success && $info['http_code'] == 200) {
			file_put_contents('1.txt', "\n - код  2pay payment." . $html, FILE_APPEND | LOCK_EX);

			$cards_is_set = Cards::find()->andWhere(['card_last_four' => $html_->Model->CardLastFour, 'user_id' => $html_->Model->AccountId])->one();

			if (empty($cards_is_set)) {
				$cards = new Cards();
				$cards->name = (isset($html_->Model->Name) ? $html_->Model->Name : 'errore');
				$cards->token = (isset($html_->Model->Token) ? $html_->Model->Token : NULL);
				$cards->card_last_four = $html_->Model->CardLastFour;
				$cards->card_exp_date = $html_->Model->CardExpDate;
				$cards->card_type = $html_->Model->CardType;
				$cards->user_id = $html_->Model->AccountId;
				$cards->save();
			}

			return $html_;
		}

		if ($info['http_code'] == 400) {
			file_put_contents('1.txt', "\n cron variant - 3код  pay payment." . $html, FILE_APPEND | LOCK_EX);
			return $html_;
		}
	}

	/**
	 * ПЛАТЕЖ СОХРАНЕННОЙ КАРТОЙ
	 * 
	 * POST запрос на адрес<br>
	 * baseURL/apiking/siteapi/paysavecard<br>
	 * отправить {"order_id":"id заказа", "token":"токен"}<br>
	 * header 'Accept:application/json'<br>
	 * header 'Content-Type:application/json'<br>
	 * Возвращается<br>
	 * @return string
	 */
	public function actionPaysavecard()
	{
		$remote_addr = $_SERVER['REMOTE_ADDR'];
		$order_id = \Yii::$app->request->post("order_id");

		$order = Orders::findOne($order_id);

		$sum_real = $order->realSum();

		$data = json_encode(
			array(
				"Amount" => $sum_real,
				"Currency" => "KZT",
				"InvoiceId" => $order_id,
				"IpAddress" => $remote_addr,
				"Description" => "Оплата товаров в kingfisher.kz",
				"AccountId" => $order->user_id,
				"Token" => \Yii::$app->request->post("token")
			)
		);

		$username = \Yii::$app->params['cloudpayments']['public_id'];

		$password = \Yii::$app->params['cloudpayments']['api_key'];

		//	$ch = curl_init("https://api.cloudpayments.ru/payments/tokens/charge");
		$ch = curl_init("https://api.cloudpayments.ru/payments/tokens/auth");

		$headers = array(
			'Content-Type:application/json',
			'Content-Length:' . strlen($data),
			'Authorization: Basic ' . base64_encode("$username:$password") // <---
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$html = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		$html_ = json_decode($html);

		if (!$html_->Success && $info['http_code'] == 200) {
			file_put_contents('1.txt', "\n cron variant - код  4pay payment." . $html, FILE_APPEND | LOCK_EX);

			// $cards_is_set = Cards::find()->andWhere(['card_last_four' => $html_->Model->CardLastFour, 'user_id' => $order->user_id])->one();	

			// if (empty($cards_is_set)) {
			// $cards = new Cards();
			// $cards->name = $html_->Model->Name;
			// $cards->token = 'dfd';
			// $cards->card_last_four = $html_->Model->CardLastFour;
			// $cards->card_exp_date = $html_->Model->CardExpDate;
			// $cards->card_type = $html_->Model->CardType;
			// $cards->user_id = $order->user_id;
			// $cards->save();
			// }	

			return $html_;
		}

		if ($html_->Success && $info['http_code'] == 200) {
			file_put_contents('1.txt', "\n token- код  pay payment." . $html, FILE_APPEND | LOCK_EX);

			$cards_is_set = Cards::find()->andWhere(['card_last_four' => $html_->Model->CardLastFour, 'user_id' => $order->user_id])->one();

			if (empty($cards_is_set)) {
				$cards = new Cards();
				$cards->name = (isset($html_->Model->Name) ? $html_->Model->Name : 'errore');
				$cards->token = (isset($html_->Model->Token) ? $html_->Model->Token : NULL);
				$cards->card_last_four = $html_->Model->CardLastFour;
				$cards->card_exp_date = $html_->Model->CardExpDate;
				$cards->card_type = $html_->Model->CardType;
				$cards->user_id = $order->user_id;
				$cards->save();
			}

			return $html_;
		}

		if ($info['http_code'] == 400) {
			file_put_contents('1.txt', "\n token - код  5pay payment." . $html, FILE_APPEND | LOCK_EX);
			return $html_;
		}
	}
}