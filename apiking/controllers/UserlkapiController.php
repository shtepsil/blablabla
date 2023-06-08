<?php
namespace apiking\controllers;

use common\models\Items;
use apiking\components\Simpleimage;
use common\models\ItemImg;
use apiking\form\LoginForm;
use apiking\form\EditAddress;
use apiking\widgets\ActiveForm;
use apiking\form\SignupForm;
use apiking\form\EditLk;
use apiking\form\EditSubs;
use apiking\form\EditPassword;
use common\models\User;
use common\models\Pickpoint;
use common\models\ItemFavorites;
use Yii;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\web\Response;
use backend\models\Pages;
use common\models\News;
use yii\data\Pagination;
use yii\web\BadRequestHttpException;
use common\models\Orders;
use common\models\HistoryBonus;
use common\models\UserAddress;
use common\models\OrdersItems;
use common\models\Cards;

use yii\filters\auth\HttpHeaderAuth;

/**
* Описание личного кабинета для авторизованного пользователя 'K I N G F I S H E R'
*
* baseURL = https://kingfisher.kz
*
* @author 'kingfisher'
* 
*/
class UserlkapiController extends MainController
{
    public $modelClass = ItemFavorites::class;
 
	/**
	* @ignore
	*/ 
	protected function verbs()
	{
		return [
			'orders' => ['POST', 'GET'],
			'order' => ['POST', 'GET'],
			'bonus' => ['GET'],
			'address' => ['GET'],
			'addaddress' => ['POST'],
			'editaddress' => ['POST'],
			'deleteaddress' => ['POST'],
			'settings' => ['POST'],
			'editsubs' => ['POST'],
			'editpassword' => ['POST'],
			'addfoto' => ['POST'],
			'additemfavorite' => ['GET'],
			'getprofile' => ['GET'],
			'getallcardsoneuser' => ['GET'],
			'deleteonecardsoneuser' => ['POST']						
		];
	}
    
	/**
	* @ignore
	*/
	public function behaviors()
    {		
		$behaviors = parent::behaviors();
		$behaviors['authenticator']['class'] = QueryParamAuth::className();
		$behaviors['authenticator']['only'] = ['test', 'orders', 'order', 'bonus', 'address', 'addaddress', 'editaddress', 'deleteaddress','settings', 'editsubs', 'editpassword', 'getprofile', 'addfoto', 'deletefoto', 'getallcardsoneuser', 'deleteonecardsoneuser'];
		$behaviors['authenticator']['tokenParam'] = 'key';
		return $behaviors;		
    }

	/**
	* @ignore
	*/
	public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);  
        return $actions;
    }
	
	/**
	* ПОЛУЧИТЬ ДАННЫЕ ПРОФИЛЯ В ЛИЧНОМ КАБИНЕТЕ
	* 
	* GET запрос на адрес<br>
	*  baseURL/apiking/userlkapi/getprofile?key=$key  <br>
	* где $key - это ключ полученный при авторизации или регистрации<br>
	* header 'Accept:application/json'<br>
	* header 'Content-Type:application/json'<br>
	* Отправить json <br>	
	* {<br>
	*	"city":2,<br>
	*   "street":"Улица",<br>
	*			"home":"Дом",<br>
	*			"house":"Квартира",<br>
	*			"phone":"Телефон",<br>
	*  "isMain":"0"<br>
	* }<br>	
	* Возвращается объект данных<br>
	* "message": {<br>
	*"success": "Успешно сохранено!"<br>
	*},<br>
	* @return array 
	*/
	public function actionGetprofile()
    {		
		$model = User::findOne(Yii::$app->user->id);
		
		$model_ = [];
		
		foreach ($model as $key => $result) {
			
			if ($key == 'photo') {
				
				$photo = '';
			
			if (!empty($result)) {				
				$photo = 'https://' . $_SERVER['HTTP_HOST'] . '/apiking/web/uploads/profile/'. $result;
			}
			
			$model_[$key] = $photo;
				
			} else {
				$model_[$key] = $result;
			}	
		}
		
        if ($model) {
			return $model_;
        } else {
            throw new BadRequestHttpException('Данный пользователь не найдён');
        }
    }

	//W2LG7JMaCr5o4a2YjOUUq8z5GMXLshHz
	/**
	* ПОЛУЧИТЬ ИНФОРМАЦИЯ ОБ ВСЕХ ЗАКАЗАХ ИЗ ЛИЧНОГО КАБИНЕТА
	* 
	* GET запрос на адрес<br>
	*  baseURL/apiking/userlkapi/orders?key=$key&offset=$offset&limit=$limit,  <br>
	*	где $key - это ключ полученный при авторизации или регистрации<br>
	"offset":"смещение(по умолчанию 0)", "limit":"количество за раз(по умолчанию 10)"
	* Возвращается <br>
	* [<br>
	* &#8195;{<br>
	*&#8195;"id": 12462,<br>
	*&#8195;"user_id": 6194,<br>
	*&#8195;"user_name": "имя",<br>
	*&#8195;"user_phone": "+7(877)-724-1111",<br>
	*&#8195;"user_mail": "qqq@gmail.com",<br>
	*&#8195;"user_address": "г.Алматы,",<br>
	*&#8195;"user_comments": "",<br>
	*&#8195;"city_id": 1,<br>
	*&#8195;"isEntity": 0,<br>
	*&#8195;"date_delivery": 1587489325,<br>
	*&#8195;"time_delivery": "15:00-19:00",<br>
	*&#8195;"code": null,<br>
	*&#8195;"full_price": 11835,<br>
	*&#8195;"full_purch_price": 7500,<br>
	*&#8195;"discount": null,<br>
	*&#8195;"price_delivery": 0,<br>
	*&#8195;"payment": "1",<br>
	*&#8195;"bonus_use": 0,<br>
	*&#8195;"bonus_add": 118,<br>
	*&#8195;"bonus_manager": null,<br>
	*&#8195;"bonus_driver": null,<br>
	*&#8195;"status": 0,<br>
	*&#8195;"pay_status": null,<br>
	*&#8195;"manager_id": null,<br>
	*&#8195;"driver_id": null,<br>
	*&#8195;"collector_id": null,<br>
	*&#8195;"created_at": 1587489325,<br>
	*&#8195;"updated_at": 1587489325,<br>
	*&#8195;"isFast": 0,<br>
	*&#8195;"isWholesale": 0,<br>
	*&#8195;"id_1c": null,<br>
	*&#8195;"enable_bonus": 1,<br>
	*&#8195;"promo_code_id": null,<br>
	*&#8195;"admin_comments": null,<br>
	*&#8195;"isPhoneOrder": 0<br>
	*&#8195;}<br>
	*]<br>
	* @return string 
	*/	
	public function actionOrders($offset = 0, $limit = 10)
    { 
		$pickpoints_ = [];
		$pickpoints = Pickpoint::find()->andWhere(['active' => 1])->all();
		
		foreach ($pickpoints as $result) {
			$pickpoints_[$result['id']] = $result['name'];
		}
	
		$order_ = [];
		
		if ($order = Orders::find()
			->joinWith([
				'ordersSets',
				'ordersItems',
			])
			->andWhere('`orders_sets`.id is NOT NULL OR `orders_items`.id is NOT NULL')
			->andWhere(['user_id' => Yii::$app->user->id])
			->with(['ordersItems.item', 'ordersSets.set'])->orderBy(['id' => SORT_DESC])->limit($limit)->offset($offset)
			->all()) {
				
			$orders = [];
			foreach ($order as $result) {
				$result['pickpoint_id'] = (isset($pickpoints_[$result['pickpoint_id']]) ? $pickpoints_[$result['pickpoint_id']] : '');
				
				$result['full_price']  = (($result->full_price + $result->price_delivery) - $result->discount($result->full_price)) - $result->bonus_use;
				
				$orders[] = $result;
			}	
			
			return $orders;
		} else {
			return $order_;
		}		   
    }
	
	/**
	* ПОЛУЧИТЬ ИНФОРМАЦИЯ ОБ ОДНОМ ЗАКАЗЕ ИЗ ЛИЧНОГО КАБИНЕТА
	* 
	* GET запрос на адрес<br>
	*  baseURL/apiking/userlkapi/order?id=$id&key=$key  <br>
	* где $id - это id товара, $key - это ключ полученный при авторизации или регистрации
	* Возвращается массив данных<br>
	* array [<br>
	*"id": 12462,<br>
	*"user_id": 6194,<br>
	*"user_name": "имя",<br>
	*"user_phone": "+7(877)-724-1111",<br>
	*"user_mail": "qqq@gmail.com",<br>
	*"user_address": "г.Алматы,",<br>
	*"user_comments": "",<br>
	*"city_id": 1,<br>
	*"isEntity": 0,<br>
	*"date_delivery": 1587489325,<br>
	*"time_delivery": "15:00-19:00",<br>
	*"code": null,<br>
	*"full_price": 11835,<br>
	*"full_purch_price": 7500,<br>
	*"discount": null,<br>
	*"price_delivery": 0,<br>
	*"payment": "1",<br>
	*"bonus_use": 0,<br>
	*"bonus_add": 118,<br>
	*"bonus_manager": null,<br>
	*"bonus_driver": null,<br>
	*"status": 0,<br>
	*"pay_status": null,<br>
	*"manager_id": null,<br>
	*"driver_id": null,<br>
	*"collector_id": null,<br>
	*"created_at": 1587489325,<br>
	*"updated_at": 1587489325,<br>
	*"isFast": 0,<br>
	*"isWholesale": 0,<br>
	*"id_1c": null,<br>
	*"enable_bonus": 1,<br>
	*"promo_code_id": null,<br>
	*"admin_comments": null,<br>
	*"isPhoneOrder": 0<br>
	*}]<br>
	*]
	* @return array 
	*/	
	public function actionOrder()
    {  
		$prePath = Yii::$app->function_system->getPrePathPictures();
		
		$pickpoints_ = [];
		$pickpoints = Pickpoint::find()->andWhere(['active' => 1])->all();
		
		foreach ($pickpoints as $result) {
			$pickpoints_[$result['id']] = $result['name'];
		}
				
		$order_ = [];
		
        if ($id = Yii::$app->request->get('id')) {
			
				$orders_items = OrdersItems::find()->andWhere(['order_id' => $id])->all();
								
				foreach ($orders_items as $result) {
					$new_orders_items = [];
					
					$itemImg = ItemImg::find()
					->andWhere(['item_id' => $result->item_id])
					->all();
					
					$result_data = json_decode($result->data);

					if ($result_data->img_list == '' AND isset($itemImg[0])) {
						$img_list = $itemImg[0]['url'];
					} else {
						$img_list = $result_data->img_list;
					}

					foreach ($result as $key => $next) {
						
						if ($key == 'data') {
							
							if (empty($next)) {
								$item_ = Items::findOne($result['item_id']);
								$new_orders_items[$key] = $item_;
							} else {
								$new_orders_items[$key] = json_decode($next);
							}
						} else {
							$new_orders_items[$key] = $next;
						}		
					}	 
							
					$ordersItemsHandings_ = [];
					$ordersItemsHandings = $result->ordersItemsHandings;
					if (!empty($ordersItemsHandings)) {
						foreach ($ordersItemsHandings as $result_) {
						  
						  $result_['type_handling_id'] = $result_->typeHandling->name;

						  $ordersItemsHandings_[] = $result_;
						}
					}

					$new_orders_items['type_handling'] = $ordersItemsHandings_;
					$new_orders_items['image'] = $img_list;
					$new_orders_items['prePath'] = $prePath;

					$data[] = $new_orders_items;
				}
			
            if ($order = Orders::find()
                ->joinWith([
                    'ordersSets',
                    'ordersItems',
                ])
                ->andWhere('`orders_sets`.id is NOT NULL OR `orders_items`.id is NOT NULL')
                ->andWhere(['orders.id' => $id, 'user_id' => Yii::$app->user->id])
                ->with(['ordersItems.item', 'ordersSets.set'])
                ->one()) {
						
				$order->pickpoint_id = (isset($pickpoints_[$order->pickpoint_id]) ? $pickpoints_[$order->pickpoint_id] : '');  	
				$full_price_final = (($order->full_price + $order->price_delivery) - $order->discount($order->full_price)) - $order->bonus_use;
				
				$final = [
					'full_price_final' => $full_price_final,
					'order' =>$order,
					'items' =>$data,
				];
                return $final;
            } else {
				return $order_;
			}	
		
        } else {
			return $order_;
		}
    }
	
	/**
	* ПОЛУЧИТЬ ИНФОРМАЦИЯ ОБ БОНУСАХ И ИХ ИСТОРИЮ ИЗ ЛИЧНОГО КАБИНЕТА
	* 
	* GET запрос на адрес<br>
	*  baseURL/apiking/userlkapi/bonus?key=$key  <br>
	* где $key - это ключ полученный при авторизации или регистрации
	* Возвращается массив данных<br>
	* array [<br>
	*"add_bonus": "Количество балов, которые пользователь получит за каждые 100 потраченных тенге(согласно сайта)",<br>
	*"history_bonus": [массив истории бонусов],<br>
	*"count": "количество бонусов",<br>
	*}]<br>
	*]
	* @return array 
	*/	
	public function actionBonus()
    {
		$history_bonus = HistoryBonus::find()->andWhere(['user_id' => Yii::$app->user->id])->limit(20)->orderBy(['created_at' => SORT_DESC])->all();		
		$percent_bonus = \Yii::$app->function_system->percent();
		$add_bonus=floor((100 * ($percent_bonus)) / 100);
		$data['add_bonus'] = $add_bonus;
		$data['history_bonus'] = $history_bonus;
		$data['count'] = \Yii::$app->user->identity->bonus;
        return $data;
    }
	
	/**
	* ПОЛУЧИТЬ ИНФОРМАЦИЮ ОБ АДРЕСАХ ИЗ ЛИЧНОГО КАБИНЕТА
	* 
	* GET запрос на адрес<br>
	*  baseURL/apiking/userlkapi/address?key=$key  <br>
	* где $key - это ключ полученный при авторизации или регистрации
	* Возвращается объект данных<br>
	* {<br>
	*&#8195;	{ "data_city(перечень всех городов)":<br>
	*	&#8195;&#8195;	{<br>
	*	&#8195;&#8195;&#8195;	1: "Алматы",<br>
	*	&#8195;&#8195;&#8195;	2: "Астана",<br>
	*	&#8195;&#8195;&#8195;	3: "Караганда",<br>
	*	&#8195;&#8195;&#8195;	4: "Шымкент",<br>
	*	&#8195;&#8195;&#8195;	5: "Атырау"<br>
	*	&#8195;&#8195;	}<br>
	*	&#8195;},<br>
	*	"list_address(массив адресов)":[{<br>
	*		"id": 664,<br>
	*	"user_id": 6190,<br>
	*	"city": 1,<br>
	*	"street": "улица",<br>
	*	"home": "12",<br>
	*	"house": "1",<br>
	*	"phone": "+7(111)-121-2121",<br>
	*	"isMain": 1 - адрес доставки по умолчанию, а если 0 то дополнительный<br>
	*	}]		
	* @return string
	*/
	public function actionAddress()
    {		
		//$list_address = UserAddress::find()->andWhere(['user_id' => Yii::$app->user->id])->orderBy(['isMain' => SORT_DESC])->all();
		$list_address = UserAddress::find()->andWhere(['user_id' => Yii::$app->user->id])->orderBy(['id' => SORT_DESC])->all();
		$data_city = \Yii::$app->function_system->data_city;
		$data['data_city'] = $data_city;
		$data['list_address'] = $list_address;
		return $data;
    }
	
	/**
	* ЗАГРУЗИТЬ ФОТО В ЛИЧНОМ КАБИНЕТЕ
	* 
	* 	POST запрос на адрес<br>
	*  baseURL/apiking/userlkapi/addfoto?key=$key  <br>
	* передать json параметр {"photo":"строка Base64", "height":"высота", "width":"ширина фото"}<br>
	* где $key - это ключ полученный при авторизации или регистрации
	* Возвращается объект данных<br>		
	* @return string
	*/
	public function actionAddfoto()
    {	
		$photo = Yii::$app->request->post('photo');
		$height = Yii::$app->request->post('height');
		$width = Yii::$app->request->post('width');

		$photo = $this->base64($photo, 'profile');

		$user = User::findOne(Yii::$app->user->id);
		$photo_ = $user->photo;
		$user->photo = $photo;
		$user->save();
		
		if (!empty( $photo_ )) {
			unlink("uploads/profile/{$photo_}");
		}
		
		$img_file = "uploads/profile/{$photo}";
		$image_ = new Simpleimage();
		$image_->load($img_file);
		$image_->resize($width, $height);
		$image_->save("uploads/profile/{$photo}");	

		return $photo;
    }
	
	/**
	* УДАЛИТЬ ФОТО В ЛИЧНОМ КАБИНЕТЕ
	* 
	* POST запрос на адрес<br>
	*  baseURL/apiking/userlkapi/deletefoto?key=$key  <br>
	* передать json параметр {}<br>
	* где $key - это ключ полученный при авторизации или регистрации
	* Возвращается объект данных<br>		
	* @return string
	*/
	public function actionDeletefoto()
    {	
		$user = User::findOne(Yii::$app->user->id);
		$photo_ = $user->photo;
		$user->photo = null;
		$user->save();
		
		if (!empty( $photo_ )) {
			 unlink("uploads/profile/{$photo_}");
			 $result['message']['success'] = 'Успешно удалено!';
		} else {
			$result['message']['success'] = 'Фото не найдено!';
		}
		
		return $result;		
    }
	
	public function base64($photo, $directory) 
	{    
		$bin = base64_decode($photo);
		$size = getImageSizeFromString($bin);
		if (empty($size['mime']) || strpos($size['mime'], 'image/') !== 0) {
			die('Base64 value is not a valid image');
		}
		$ext = substr($size['mime'], 6);

		if (!in_array($ext, ['png', 'gif', 'jpeg','svg'])) {
			die('Unsupported image type');
		}
		
		$imgName = md5(microtime() . rand(0, 9999)).".{$ext}";
	
		$img_file = "uploads/{$directory}/{$imgName}";
		file_put_contents($img_file, $bin);      
		return $imgName;
	}
		
	/**
	* ДОБАВИТЬ АДРЕС В ЛИЧНОМ КАБИНЕТЕ
	* 
	* POST запрос на адрес<br>
	*  baseURL/apiking/userlkapi/addaddress?key=$key  <br>
	* где $key - это ключ полученный при авторизации или регистрации<br>
	* header 'Accept:application/json'<br>
	* header 'Content-Type:application/json'<br>
	* Отправить json <br>	
	* {<br>
	*	"city":2,<br>
	*   "street":"Улица",<br>
	*			"home":"Дом",<br>
	*			"house":"Квартира",<br>
	*			"phone":"Телефон",<br>
	*  "isMain":"0",<br>
	*  "name":"имя"<br>
	* }<br>	
	* Возвращается объект данных<br>
	* "message": {<br>
	*"success": "Успешно сохранено!"<br>
	*},<br>
	* @return array 
	*/
	public function actionAddaddress()
    {
		$model = new EditAddress();
		$model->load(Yii::$app->request->bodyParams, '');
		$result = $model->send();
		
		if ($result) {
			return $result;
		} else {
			return $model;
		}
    }
	
	/**
	* ИЗМЕНИТЬ АДРЕС В ЛИЧНОМ КАБИНЕТЕ
	* 
	* POST запрос на адрес<br>
	*  baseURL/apiking/userlkapi/editaddress?key=$key&id=$id  <br>
	* где $id - это id адреса, $key - это ключ полученный при авторизации или регистрации<br>
	* header 'Accept:application/json'<br>
	* header 'Content-Type:application/json'<br>
	* Отправить json <br>	
	* {<br>
	*	"city":2,<br>
	*   "street":"Улица",<br>
	*			"home":"Дом",<br>
	*			"house":"Квартира",<br>
	*			"phone":"Телефон",<br>
		*  "name":"имя",<br>
	*  "isMain":"0"<br>
	* }<br>	
	* Возвращается объект данных<br>
	* "message": {<br>
	*"success": "Успешно сохранено!"<br>
	*},<br>
	* @return array 
	*/
	public function actionEditaddress($id)
    {		
        $address = UserAddress::find()->andWhere(['user_id' => Yii::$app->user->id, 'id' => $id])->one();
        if ($address) {
			$model = new EditAddress();
			$model->setAttributes($address->attributes, false);
			$model->load(Yii::$app->request->bodyParams, '');
			$result = $model->send();

			if ($result) {
				return $result;
			} else {
				return $model;
			}
        } else {
            throw new BadRequestHttpException('Данный адрес не найдён');
        }
    }
	
	/**
	* УДАЛИТЬ АДРЕС В ЛИЧНОМ КАБИНЕТЕ
	* 
	* POST запрос на адрес<br>
	*  baseURL/apiking/userlkapi/deleteaddress?key=$key&id=$id  <br>
	* где $id - это id адреса, $key - это ключ полученный при авторизации или регистрации<br>
	* header 'Accept:application/json'<br>
	* header 'Content-Type:application/json'<br>
	* Отправить json <br>	
	* {}<br>	
	* Возвращается объект данных<br>
	* "message": {<br>
	*"success": "Успешно сохранено!"<br>
	*},<br>
	* @return array 
	*/
	public function actionDeleteaddress($id)
    {			
		$address = UserAddress::findOne($id);	
		
		$isMain=$address->isMain;

        if ($address) {
				
			$address->delete();	
			
			if ($isMain) {
	
					$address_ = UserAddress::find()->andWhere(['user_id' => Yii::$app->user->id])->orderBy(['id' => SORT_DESC])->one();

					if ($address_) {
						$address_->isMain = '1';
						$address_->save();
					}
				}
			
			$result['message']['success'] = 'Успешно удалено!';			
			return $result;
        } else {
            throw new BadRequestHttpException('Данный адрес не найдён');
        }
    }
	
	/**
	* ИЗМЕНИТЬ НАСТРОЙКИ В ЛИЧНОМ КАБИНЕТЕ
	* 
	* POST запрос на адрес<br>
	*  baseURL/apiking/userlkapi/settings?key=$key  <br>
	* где $key - это ключ полученный при авторизации или регистрации<br>
	* header 'Accept:application/json'<br>
	* header 'Content-Type:application/json'<br>
	* Отправить json <br>	
	* {<br>
	*	"username":"Имя",<br>
	* "email": "email",<br>
	*	"phone": "+7(111)-222-2222",<br>
	* "dob": "12/12/2000",<br>
	* "sex": "1 - мужской пол, 2 - женский, 0 - не установлен"<br>
	* }<br>	
	* Возвращается объект данных<br>
	* "message": {<br>
	*"success": "Успешно сохранено!"<br>
	*},<br>
	* @return array 
	*/
	public function actionSettings()
    {		
		$model = new EditLk();
		$model->load(Yii::$app->request->bodyParams, '');
		
		$result = $model->send();
		
		if ($result) {
			return $result;
		} else {
			return $model;
		}		
    }

	/**
	* УСТАНОВЛЕНИЕ РАССЫЛКИ И УВЕДОМЛЕНИЙ В НАСТРОЙКАХ В ЛИЧНОМ КАБИНЕТЕ
	* 
	* POST запрос на адрес<br>
	*  baseURL/apiking/userlkapi/editsubs?key=$key  <br>
	* где $key - это ключ полученный при авторизации или регистрации<br>
	* header 'Accept:application/json'<br>
	* header 'Content-Type:application/json'<br>
	* Отправить json <br>	
	* {<br>	
	* "isSubscription":"0 - не установлено, 1 - установлено",
	*"isNotification":"0 - не установлено, 1 - установлено"
	* }<br>	
	* Возвращается объект данных<br>
	* "message": {<br>
	*"success": "Успешно сохранено!"<br>
	*},<br>
	* @return array 
	*/
	public function actionEditsubs()
    {		
		$model = new EditSubs();
		$model->load(Yii::$app->request->bodyParams, '');
		$result = $model->send();
		return $result;
    }
	
	/**
	* СМЕНА ПАРОЛЯ В НАСТРОЙКАХ В ЛИЧНОМ КАБИНЕТЕ
	* 
	* POST запрос на адрес<br>
	*  baseURL/apiking/userlkapi/editpassword?key=$key  <br>
	* где $key - это ключ полученный при авторизации или регистрации<br>
	* header 'Accept:application/json'<br>
	* header 'Content-Type:application/json'<br>
	* Отправить json <br>	
	* {<br>	
	*    'password1':'Новый пароль',<br>	
	*    'password2':'Повторите пароль',<br>	
	* }<br>	
	* Возвращается объект данных<br>
	* "message": {<br>
	*"success": "Успешно сохранено!"<br>
	*},<br>
	* @return array 
	*/
	public function actionEditpassword()
    {		
		$model = new EditPassword();
		$model->load(Yii::$app->request->bodyParams, '');
		
		$result = $model->send();
		
		if ($result) {
			return $result;
		} else {
			return $model;
		}
    }
	 
	/**   nZ6KGc3AbLifHGARbdw4RC4mWctaz-uS
	* ДОБАВИТЬ ТОВАР В ИЗБРАННОЕ   
	* 
	* GET запрос на адрес<br>
	*  baseURL/apiking/userlkapi/additemfavorite?key=$key&item_id=$item_id  <br>
	* где $key - это ключ полученный при авторизации или регистрации<br>
	* header 'Accept:application/json'<br>
	* header 'Content-Type:application/json'<br>
	* Отправить json <br>	
	* Возвращается объект данных<br>
	* "message": {<br>
	*"success": "Успешно сохранено!"<br>
	*},<br>
	* @return array 
	*/
	public function actionAdditemfavorite()
    {	
	
		$auth_token = Yii::$app->request->get('key');

		$user = User::findIdentityByAccessToken($auth_token);

		if (isset($user)) {

			$item_id = Yii::$app->request->get('item_id');		

			$count_item_user = ItemFavorites::find()->andWhere(['user_id' => $user->id, 'item_id' => $item_id])->count();
			$result = true;
			
			if ($count_item_user == 0) {
				$favorites = new ItemFavorites();
				$favorites->item_id = $item_id;
				$favorites->user_id =	$user->id;
				$result = $favorites->save(false);
			}
			return [
				"result" => $result
			];
		} else {
			return [
				'result' => 'no_authorization'
			];
		}	
	}
	
	/**
	* ПОЛУЧИТЬ ТОВАРЫ ОДНОГО ПОЛЬЗОВАТЕЛЯ В РАЗДЕЛЕ ИЗБРАННЫЕ   
	* 
	* GET запрос на адрес<br>
	*  baseURL/apiking/userlkapi/getitemfavorite?key=$key  <br>
	* где $key - это ключ полученный при авторизации или регистрации<br>
	* header 'Accept:application/json'<br>
	* header 'Content-Type:application/json'<br>
	* Отправить json <br>	
	* Возвращается объект данных<br>
	* "message": {<br>
	*"success": "Успешно сохранено!"<br>
	*},<br>
	* @return array 
	*/
	public function actionGetitemfavorite()
    {	
	  
		$prePath = Yii::$app->function_system->getPrePathPictures();
	
		$auth_token = Yii::$app->request->get('key');

		$user = User::findIdentityByAccessToken($auth_token);

		if (isset($user)) {

			$item_user = ItemFavorites::find()->andWhere(['user_id' => $user->id])->all();
			$data = [];
			
			foreach ($item_user as $result) {

                $result->item->price = $result->item->real_price();
							
				// $itemImg = ItemImg::find()
					// ->andWhere(['item_id' => $result->item_id])
					// ->all();
					
				// $result_data = json_decode($result->data);

				// if ($result_data->img_list == '') {
					// $img_list = $itemImg[0]['url'];
				// } else {
					// $img_list = $result_data->img_list;
				// }
				
				$itemImg = ItemImg::find()
				->andWhere(['item_id' => $result->item->id])
				->all();

				if ($result->item->img_list == '') {
					$img_list = $itemImg[0]['url'];
				} else {
					$img_list = $result->item->img_list;
				}
				
				

				$data[] = [
				    "prePath" => $prePath,
				    "img_list" => $img_list,
					"favorite_id" => $result->id,
					"item_id" => $result->item->id,
					"item_name" => $result->item->name,
					"item_price" => $result->item->price,
					"item_old_price" => $result->item->old_price,
				];
			}
			
			return [
				"result" => $data
			];
		} else {
			return [
				'result' => 'no_authorization'
			];
		}	
	}
	
	/**
	* УДАЛИТЬ ОДИН ТОВАР ИЗ ИЗБРАННОГО   
	* 
	* GET запрос на адрес<br>
	*  baseURL/apiking/userlkapi/deleteitemfavorite?key=$key&item_id=$item_id  <br>
	* где $key - это ключ полученный при авторизации или регистрации<br>
	* header 'Accept:application/json'<br>
	* header 'Content-Type:application/json'<br>
	* Отправить json <br>	
	* Возвращается объект данных<br>
	* "message": {<br>
	*"success": "Успешно сохранено!"<br>
	*},<br>
	* @return array 
	*/
	public function actionDeleteitemfavorite()
    {	
	
		$auth_token = Yii::$app->request->get('key');
 
		$user = User::findIdentityByAccessToken($auth_token);

		if (isset($user)) {

			$item_id = Yii::$app->request->get('item_id');		

			$count_item_user = ItemFavorites::find()->andWhere(['user_id' => $user->id, 'item_id' => $item_id])->one();
			$result = true;
			
			if ($count_item_user) {		
				$count_item_user->delete();
			}

			return [
				"result" => $result
			];
		} else {
			return [
				'result' => 'no_authorization'
			];
		}	
	}
	  
	/**
	* ПОЛУЧИТЬ ВСЕ КАРТЫ ОДНОГО ЮЗЕРА
	* 
	* GET запрос на адрес<br>	
	*  baseURL/apiking/userlkapi/getallcardsoneuser?key=$key  <br>
	* где $key - это ключ полученный при авторизации или регистрации<br>
	* Возвращается массив полных данных о городах<br>
	* @return string
	*/
	public function actionGetallcardsoneuser()
    {
        $data = [];
        $data = Cards::find()->where(['user_id' => Yii::$app->user->id])->all();
        return $data;
    }
	
	/**
	* УДАЛИТЬ ОДНУ КАРТУ ОДНОГО ЮЗЕРА
	* 
	* POST запрос на адрес<br>
	*  baseURL/apiking/userlkapi/deleteonecardsoneuser?key=$key<br> 
	* отправить {"id":"id записи в базе карт"}<br>	
	* Возвращается массив полных данных о городах<br>
	* @return string
	*/
	public function actionDeleteonecardsoneuser()
    {
	
		$id = \Yii::$app->request->post("id");
        $data = Cards::find()->andWhere(["id" => $id, "user_id" => Yii::$app->user->id])->one();
		if ($data){
			$data->delete();
		}
		
        return $data;
    }
}
