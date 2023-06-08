<?php

namespace apiking\form;

use common\components\Debugger as d;
use common\components\api\onesignal\ApiOneSignal;
use common\components\retailcrm\ApiHelper;
use common\models\Items;
use common\models\Orders;
use common\models\OrdersHistory;
use common\models\OrdersItems;
use common\models\Pickpoint;
use common\models\PromoCode;
use common\models\Sets;
use common\models\User;
use common\models\UserAddress;
use frontend\models\Delivery;
use frontend\models\retailcrm\CreateCrmOrder;
use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\HttpException;
use backend\models\SUser;

class Order extends Model
{
    //region Информация о покупателе
    public $first_name;
    public $last_name;
    public $phone;
    public $email;
    public $isEntity = 0;
    //endregion
    //region Адрес доставки
    public $city;
    public $street;
    public $home;
    public $house;
	public $coordinates_json_yandex;
	public $delivery_yandex;
	public $delivery_method = 0;
	public $pickpoint_id;
    //endregion
    //region Информация о заказе
    public $address_id;
    public $payment;
    public $time_order;
    public $bonus = 0;
    public $code;
    public $comments;
    //endregion
	public $time_delivery = "123";
    public $type_delivery = 1;//способ получения 1= доставка, 0= самовывоз
    public $only_pickup = 0;
    public $our_stories_id = null;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //region Информация о покупателе
            [['first_name', 'last_name', 'email', 'phone'], 'trim', 'on' => ['isGuest']],
            [['first_name', 'last_name'], 'required', 'on' => ['isGuest']],
            [['first_name', 'last_name','coordinates_json_yandex'], 'string', 'max' => 255],
            [['phone'], 'trim'],
            [['phone'], 'required'],
            ['email', 'email', 'on' => ['isGuest']],
//            ['email', 'unique', 'targetClass' => User::className(), 'targetAttribute' => 'email', 'on' => ['isGuest']],
            ['isEntity', 'boolean', 'on' => ['isGuest']],
            //endregion
            //region Адрес доставки
            [['street', 'home'], 'trim', 'on' => ['isGuest', 'no_address']],
            [['city', 'street', 'home'], 'required', 'on' => ['isGuest', 'no_address'], 'isEmpty' => [$this, 'no_delivery_required']],
            [['city', 'our_stories_id', 'pickpoint_id'], 'integer'],
            [['street', 'home', 'house'], 'string', 'max' => 255],
			[['delivery_yandex', 'delivery_method'], 'integer'],
            //endregion
            //region Информация о заказе
            [['phone'], 'match', 'pattern' => '/^((\+?7)(\(?\d{3})\)-?)?(\d{3})(-?\d{4})$/', 'message' => \Yii::t('main', 'Некорректный формат поля {attribute}')],
            ['address_id', 'required', 'on' => 'is_address', 'isEmpty' => [$this, 'no_delivery_required']],
            [['payment'], 'required'],
            [['time_order'], 'required', 'isEmpty' => [$this, 'no_delivery_required']],
            [['bonus'], 'boolean'],
            [['code', 'comments'], 'string', 'max' => 255],
            [['time_order', 'bonus', 'code'], 'safe'],
            //endregion
            //region Безопасные аттрибуты, без этого форма не будет принимать
            [['city', 'street', 'home', 'house', 'first_name', 'last_name', 'email', 'phone', 'address_id', 'payment', 'isEntity', 'type_delivery', 'only_pickup'], 'safe'],
            //endregion
        ];
    }
    public function no_delivery_required($value)
    {
        if ($this->type_delivery == 0 || $this->only_pickup == 1) {
            return false;
        } else {
            return $value === null || $value === [] || $value === '';
        }
    }
    /**
     * Returns the form name that this model class should use.
     *
     * The form name is mainly used by [[\yii\widgets\ActiveForm]] to determine how to name
     * the input fields for the attributes in a model. If the form name is "A" and an attribute
     * name is "b", then the corresponding input name would be "A[b]". If the form name is
     * an empty string, then the input name would be "b".
     *
     * By default, this method returns the model class name (without the namespace part)
     * as the form name. You may override it when the model is used in different forms.
     *
     * @return string the form name of this model class.
     */
    public function formName()
    {
        return 'order';
    }
    /**
     * This method is invoked before validation starts.
     * The default implementation raises a `beforeValidate` event.
     * You may override this method to do preliminary checks before validation.
     * Make sure the parent implementation is invoked so that the event can be raised.
     * @return boolean whether the validation should be executed. Defaults to true.
     * If false is returned, the validation will stop and the model is considered invalid.
     */
    public function beforeValidate()
    {
        /**
         * @var $user \common\models\User
         */
//        if($this->isEntity==1){
//            $this->scenario = 'entity';
//        }
        if (\Yii::$app->user->isGuest) {
            $this->scenario = 'isGuest';
        } else {
            if (!$this->address_id || $this->address_id == 'none') {
                $this->scenario = 'no_address';
            } else {
                $this->scenario = 'is_address';
            }
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            //region Информация о покупателе
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'phone' => 'Телефон',
            'email' => 'E-Mail',
            'isEntity' => 'Юридическое лицо',
            'entity_name' => 'Юр. название',
            'entity_address' => 'Юр. адрес',
            'entity_bin' => 'БИН',
            'entity_iik' => 'ИИК',
            'entity_bank' => 'Банк',
            'entity_bik' => 'БИК',
            'entity_nds' => 'Плательщик НДС',
            //endregion
            //region Адрес доставки
            'city' => 'Город',
//            'index' => 'Почтовый индекс',
            'street' => 'Улица',
            'home' => 'Дом',
            'house' => 'Квартира',
            //endregion
            //region Информация о заказе
            'time_order' => 'Удобное время (указано местное время)',
            'address_id' => 'Адрес доставки',
            'bonus' => 'Бонус',
            'payment' => 'Способ оплаты',
            'comments' => 'Примечание к заказу',
            'code' => 'Код',
            //endregion
        ];
    }
    public $data_city = [
        1 => 'Алматы',
        2 => 'Астана'
    ];
    public $time_days = [
        //12 => '09:00-12:00',
        //15 => '12:00-15:00',
        //18 => '15:00-18:00',
        //21 => '18:00-21:00',
		15 => '15:00-19:00'
    ];
    public function getData_payment(){

        return [
           1=>\Yii::$app->settings->get('payment_type_cash') ,
           2=>\Yii::$app->settings->get('payment_type_online') ,
           3=>\Yii::$app->settings->get('payment_type_cards') ,
        ];
    }
    public function send($session_id, $key)
    {
        /**
         * @var $user \common\models\User
         * @var $functions \frontend\components\FunctionComponent
         */
		$payment = false;
	
        $functions = Yii::$app->function_system;
        $result = [];
//		$result['success'] = false;
//		$result['message'] = 'Заказы временно отключены!';
//		return $result;
		$data = [];
        $connect = \Yii::$app->db;
        $transaction = $connect->beginTransaction();
		$user = User::findIdentityByAccessToken($key);

		/*
        if ($user) { 
            if ($this->email && !User::findByUsername($this->email)) {
                $user = new User();

                if ($this->isEntity == 1) {
                    $entity = [
                        'entity_name' => '',
                        'entity_address' => '',
                        'entity_bin' => '',
                        'entity_iik' => '',
                        'entity_bank' => '',
                        'entity_bik' => '',
                        'entity_nds' => 0,
                    ];
                    $user->data = Json::encode($entity);
                }

                $user->isEntity = $this->isEntity;
                $user->email = $this->email;
                $user->city_id = $this->city;
                $user->phone = $this->phone;
                $user->username = $this->first_name . ' ' . $this->last_name;
                $user->status = $user::STATUS_ACTIVE;
                $user->password = \Yii::$app->security->generateRandomString(6);
                $user->generateAuthKey();

                if (!$user->save()) {
                    $result['message']['error'] = 'Произошла ошибка на стороне сервера!';
                    return $result;
                }
            } elseif ($this->phone) {
                $attributes_user = [];
                $attributes_user['isEntity'] = $this->isEntity;
                $attributes_user['username'] = $this->first_name . ' ' . $this->last_name;
                //$attributes_user['email'] = $this->email;
                $attributes_user['city_id'] = $this->city;

                if ($this->isEntity == 1) {
                    $entity = [
                        'entity_name' => '',
                        'entity_address' => '',
                        'entity_bin' => '',
                        'entity_iik' => '',
                        'entity_bank' => '',
                        'entity_bik' => '',
                        'entity_nds' => 0,
                    ];
                    $attributes_user['data'] = Json::encode($entity);
                }
                $user = User::checkPhone($this->phone, $attributes_user);
		
            } else {
                $user = null;
            }
        } else { 

            $update_user = false;

            if (!$user->phone) {
                $user->phone = $this->phone;
                $update_user = true;
            }

            if (!$user->city_id && $this->city) {
                $user->city_id = $this->city;
                $update_user = true;
            }

            if ($update_user) {
                $user->save(false);
            }
        }
		*/
        $time = time();
        $enable_discount = true;
        $data = [
            'user_comments' => $this->comments,
            'user_phone' => $this->phone,
            'code' => $this->code,
            'bonus_use' => 0,
            'payment' => $this->payment,
            'status' => 0,
            'city_id' => $this->city,
            'created_at' => $time,
            'updated_at' => $time,
        ];

        if ($user) {
            if (doubleval($user->discount)) {
                $data['discount'] = $user->discount . '%';
                $enable_discount = false;
            }
            $data['isWholesale'] = $user->isWholesale;
            if ($user->isWholesale == 1) {
                $enable_discount = false;
            }
        } else {
            $data['isWholesale'] = 0;
        }
        /**
         * @var $code_model PromoCode
         */
        if (($code = \Yii::$app->request->post('code'))
            && ($code_model = PromoCode::find()->andWhere(['code' => $code])->one())
            && $code_model->check_enable()
        ) {
            $enable_discount = false;
            $data['promo_code_id'] = $code_model->id;
            $data['discount'] = $code_model->discount;
        } else {
            $code_model = false;
        }

        if (!$user) {
            $data['user_name'] = $this->first_name . ' ' . $this->last_name;
            $data['user_phone'] = $this->phone;
            $data['user_mail'] = $this->email;
            $data['isEntity'] = $this->isEntity;
			
			$data['coordinates_json_yandex'] = (isset($this->coordinates_json_yandex) ? $this->coordinates_json_yandex : null);
			
			if ($this->type_delivery == 3) {
				$data['delivery_method'] = 2;
				$data['price_delivery'] = $this->delivery_yandex;
				$data['pickpoint_id'] = $this->pickpoint_id;
			}
						
            if ($this->type_delivery == 0) {
				$data['delivery_method'] = 1;
                $data['user_address'] = 'Самовывоз';
                $data['city_id'] = \Yii::$app->session->get('city_select', 1);
                $data['pickpoint_id'] = $this->our_stories_id;
            } else {
                $data['user_address'] = 'г.' . $functions->data_city[$this->city] . ', ул. ' . $this->street . ', дом. ' . $this->home . (($this->house) ? (', кв. ' . $this->house) : '');
            }
        } else {
            $data['user_id'] = $user->id;
            $data['user_name'] = $user->username;
//            $data['user_phone'] = $user->phone;
            $data['user_mail'] = $user->email;
            $data['isEntity'] = $user->isEntity;
			
			$data['coordinates_json_yandex'] = (isset($this->coordinates_json_yandex) ? $this->coordinates_json_yandex : null);

            /*
             * Если промокод найден
             */
			if($code_model !== false AND $code_model->id){
                $code_for_app_first_order = false;
                /*
                 * Если в настройках пользователя, введёный промкод уже есть,
                 * значит вернём предупреждение.
                 * Если заказ оформляется через мобильное приложение
                 * с промокодом для первого заказа из приложения.
                 */
                if($code_model->type == 'first_order_app'){
                    $code_for_app_first_order = true;
                }

                /*
			     * Если промокод найден
			     * и промокод есть в массиве настроек промокодов
			     * для первого заказа из приложения
			     */
                if($code_model->code AND $code_for_app_first_order){
                    // Если настройка first_order_app в настройках пользователя уже существует
                    if($promo_settings = $user->settings('first_order_app')){
                        /*
                         * Если у пользователя в существующих настройках
                         * ещё нет промокода для первого заказа из приложения
                         */
                        if(!in_array($code_model->code, $promo_settings)){
                            /*
                             * Добавим промокод в существующие настройки пользователя,
                             * запомним, что текущий пользователь уже использовал такой промокод!
                             */
                            $user->settings('first_order_app',$code_model->code,'set');
                        }
                    }else{
                    /*
                     * Если настройка first_order_app не ещё существует
                     * Добавим промокод в настройку
                     */
                        $user->settings('first_order_app',[$code_model->code],'set');
                    }
                }
            }

			if ($this->type_delivery == 3) {
				$data['delivery_method'] = 2;
				$data['price_delivery'] = $this->delivery_yandex; 
				$data['user_address'] = $this->street . ',' . $this->home . (($this->house) ? (', кв. ' . $this->house) : '');

				$data['pickpoint_id'] = $this->pickpoint_id;
			} 
			
            if ($this->type_delivery == 0) {
				$data['delivery_method'] = 1;
                $data['user_address'] = 'Самовывоз';
                $data['city_id'] = \Yii::$app->session->get('city_select', 1);
                $data['pickpoint_id'] = $this->our_stories_id;
            } else {
                if ($this->address_id != 'none' && $this->address_id) {
                    /**
                     * @var $address UserAddress
                     */
                    $address = UserAddress::find()->where(['id' => $this->address_id, 'user_id' => $user->id])->one($connect);
                    $data['city_id'] = $address->city;
                    $data['user_address'] = 'г.' . $address->data_city[$address->city] . ', ул. ' . $address->street . ', дом. ' . $address->home . (($address->house) ? (', кв. ' . $address->house) : '');
                } else {
                    $data['user_address'] = 'г.' . $functions->data_city[$this->city] . ', ул. ' . $this->street . ', дом. ' . $this->home . (($this->house) ? (', кв. ' . $this->house) : '');

                    if (!UserAddress::find()->where(['user_id' => $user->id])->count()) {
                        try {
                            $record_address          = new UserAddress();
                            $record_address->user_id = $user->id;
                            $record_address->city    = $this->city;
                            $record_address->street  = $this->street;
                            $record_address->home    = $this->home;
                            $record_address->house   = $this->house;
                            $record_address->phone   = $this->phone;
                            $record_address->isMain  = 1;
                            $record_address->save(false);
                        } catch (\Exception $exception) {
                            $category = get_class($exception);

                            if ($exception instanceof HttpException) {
                                $category = 'yii\\web\\HttpException:' . $exception->statusCode;
                            } elseif ($exception instanceof \ErrorException) {
                                $category .= ':' . $exception->getSeverity();
                            }
                            Yii::error($exception, $category);
                        }
                    }
                }
            }
        }

        if ($this->only_pickup == 1) {
            $data['user_address'] = 'Самовывоз';
        }

		if ($this->type_delivery == 3) { 
			//$data['user_address'] = 'г.' . $functions->data_city[$this->city] . ', ул. ' . $this->street . ', дом. ' . $this->home . (($this->house) ? (', кв. ' . $this->house) : '');
			$data['user_address'] = $this->street . ',' . $this->home . (($this->house) ? (', кв. ' . $this->house) : '');
		    $data['pickpoint_id'] = $this->pickpoint_id;
		}

        // Получим timestamp начала текущего дня ПЛЮС 11 часов.
        $today_delivery = strtotime( date('Y-m-d' . ' 00:00:00') ) + 39600;
        /*
         * Если текущее время больше 11 часов, то прибавим день (24 часа)
         * т.е. перенесём доставку на следующий день.
         */
        if($time >= $today_delivery){
            $time += 86400;
        }

        $data['date_delivery'] = $time;
		$data['time_delivery'] = "14:00-22:00";

        /**
         * @var $items Items[]
         */
        $sessions_items = Yii::$app->session->get($session_id, []);

        $sessions_type_handling = Yii::$app->session->get($session_id .'type_handling', []);
        $sessions_sets = Yii::$app->session->get($session_id .'sets', []);
        $sum = 0;
        $full_purch_price = 0;
        $data_items = $data_sets = $insert_handing = [];
        $weight = 0;

        $order = new Orders($data);

        if ($sessions_items) {
            $q = new ActiveQuery(Items::className());
            $q->indexBy('id')
                ->with('itemsTogethers')
                ->andWhere(['id' => array_keys($sessions_items)]);
            $items = $q->all();

            if ($enable_discount) {
                $discount = $functions->discount_sale_items($items, $sessions_items);
            } else {
                $discount = [];
            }

            foreach ($items as $key => $item) {
                /*
                 * Настроим цену
                 * Если текущий пользователь Оптовый,
                 * то цена будет соответствующая.
                 */
                $item->price = $item->real_price();
                $count = $sessions_items[$key];
                $handling = [];

                if (isset($sessions_type_handling[$item->id])) {
                    $handling = $sessions_type_handling[$item->id];
                }

                /*
                 * Закомментировал этот код, потому что проверки на статус "Оптовый"
                 * происходят при запуске $item->real_price();
                 *
                 */
//                if ($data['isWholesale'] == 1 && $item->wholesale_price) {
//                    $clone_item = clone $item;
//                    $price_item = $item->wholesale_price;
//
//                    if ($clone_item->discount) {
//                        $clone_item->discount = 0;
//                    }
//                    $clone_item->price = $clone_item->wholesale_price;
//                    $bonus = $clone_item->price_bonus_manager();
//                } else {
                    $bonus = $item->price_bonus_manager();
                    $price_item = $item->real_price();
//                }

                $weight += $item->weight * $count;

                $data_items[] = [
                    'order_id' => '{order_id}',
                    'item_id' => $item->id,
                    'count' => $count,
                    'price' => $price_item,
                    'weight' => (($item->measure == 0 || ($item->measure != $item->measure_price)) ? ($count * $item->weight) : 0),
                    'purch_price' => $item->purch_price,
                    'bonus_manager' => $bonus,
                    'data' => Json::encode($order->convert_to_array($item))
                ];

                if ($enable_discount) {
                    $sum += $functions->full_item_price($discount, $item, $count);
                } else {
                    $sum += $item->sum_price($count, 'main', $price_item);
                }

                $full_purch_price += $item->sum_price($count, 'purch');

                if ($handling) {
				
                    foreach ($handling as $type_handling) {
                        $insert_handing[$item->id][] = $type_handling;
                    }
                }
            }
        }

        if ($sessions_sets) {
            /**
             * @var $sets Sets[]
             */
            $q = new ActiveQuery(Sets::className());
            $q->indexBy('id')
                ->andWhere(['id' => array_keys($sessions_sets)]);
            $sets = $q->all();

            foreach ($sets as $key => $item) {
                $count = $sessions_sets[$key];
                $data_sets[] = [
                    'order_id' => '{order_id}',
                    'set_id' => $item->id,
                    'count' => $count,
                    'price' => $item->real_price(),
                    'purch_price' => $item->real_purch_price(),
                    'bonus_manager' => $item->price_bonus_manager(),
                ];

                $sum += round($count * $item->real_price());

                if ($item->real_purch_price()) {
                    $full_purch_price += round($count * $item->real_purch_price());
                } else {
                    $full_purch_price += round($count * $item->real_price());
                }
            }
        }

        $data['full_purch_price'] = $full_purch_price;

        $delivery = 0;

        if ($this->type_delivery == 0) {
            $delivery = (new Delivery())->getPickUpPrice((int)$this->city);
        }
        elseif (!empty($this->city) && (int)$this->city > 0) {
            $delivery = (new Delivery())->getDelivery($sum, $weight, (int)$this->city);
        }

		if ($this->type_delivery == 3) {
			$data['price_delivery'] = $this->delivery_yandex;
			$data['full_price'] = $sum;
			$data['pickpoint_id'] = $this->pickpoint_id;
		} else {
			$data['price_delivery'] = ($delivery > 0 ? $delivery : 0);
			$data['full_price'] = $sum;
		}

        $percent_bonus = Yii::$app->function_system->percent();

        if ($discount_price = $order->discount($sum)) {
            $sum = $sum - $discount_price;
        }

        $full_bonus = floor(((int)$sum * ($percent_bonus)) / 100);
        $data['bonus_add'] = $full_bonus;

        if (!Yii::$app->user->isGuest && $this->bonus == 1) {
            $bonus_user = (int)Yii::$app->user->identity->bonus;
            $use_bonus = 0;
            $update_bonus = false;

            if ($bonus_user) {
                if ($bonus_user >= $sum) {
                    $update_bonus = $bonus_user - $sum;
                    $use_bonus = $sum;
                } elseif ($bonus_user < $sum) {
                    $update_bonus = 0;
                    $use_bonus = $bonus_user;
                }

                if (!is_bool($update_bonus)) {
                    User::updateAll(['bonus' => $update_bonus], ['id' => Yii::$app->user->id]);
                }
            }

            $data['bonus_use'] = $use_bonus;
        }

        if($data['payment']==2){
            $data['pay_status']='wait';
        }
		$data['isApp'] = 1;

        if ($connect->createCommand()->insert('orders', $data)->execute() && ($data_items || $data_sets)) {
            \Yii::$app->session->remove($session_id .'invited_code');
            $order_id = $connect->getLastInsertID();
			
            /*запись в таблицу к выгрузке 1c*/
			$data_unloading['order_id'] = $order_id;
			$connect->createCommand()->insert('orders_unloading', $data_unloading)->execute();

            if (isset($data['bonus_use']) && $data['bonus_use']) {
                $log_data = $data;
                $log_data['id'] = $order_id;
                $connect->createCommand()->insert('s_log_action', [
                    'action' => 'user_use_bonus',
                    'data' => Json::encode($log_data),
                    'time' => $time,
                ])->execute();
            }

            $send = false;

            if ($data_items) {
                foreach ($data_items as &$order_item) {
                    $order_item['order_id'] = $order_id;
                }

                if ($connect->createCommand()
                    ->batchInsert('orders_items', ['order_id', 'item_id', 'count', 'price', 'weight', 'purch_price', 'bonus_manager', 'data'], $data_items)
                    ->execute()
                ) {
                    $send = true;

                    if ($insert_handing) {
                        $insert = [];
                        $old_items = OrdersItems::find()->where(['order_id' => $order_id])->indexBy('item_id')->all();

                        foreach ($insert_handing as $key => $value) {
                            if (isset($old_items[$key])) {
                                foreach ($value as $val) {
                                    $insert[] = [
                                        'orders_items_id' => $old_items[$key]->id,
                                        'type_handling_id' => $val
                                    ];
                                }
                            }
                        }

                        if ($insert) {
                            Yii::$app->db->createCommand()->batchInsert('orders_items_handing', [
                                'orders_items_id',
                                'type_handling_id'
                            ], $insert)->execute();
                        }
                    }
                }
            }

            if ($data_sets) {
                foreach ($data_sets as &$order_set) {
                    $order_set['order_id'] = $order_id;
                }

                if ($connect->createCommand()
                    ->batchInsert('orders_sets', ['order_id', 'set_id', 'count', 'price', 'purch_price', 'bonus_manager'], $data_sets)
                    ->execute()
                ) {
                    $send = true;
                }
            }

            if ($send) {
                if ($code_model && $code_model->type == 'one') {
                    $code_model->isEnable = 0;
                    $code_model->save(false);
                }

                $history = new OrdersHistory();
                $history->user_name = $data['user_name'];
                $history->order_id = $order_id;
                $history->action = 1;
                $history->save(false);
                $url = Url::to(['site/success-order']);
                $order_model = Orders::findOne($order_id);


                if ($this->type_delivery == 3) {
                    $suser_for_yandex = [];
                    $suser_for_yandex = SUser::findUserYandexDelivery();

                    if (!empty($suser_for_yandex)) {
                        \Yii::$app->mailer->compose(['html' => 'orderyandex'], ['order' => $order_model, 'delivery' => 'Яндекс доставка', 'name_pickup' => ''])
                        ->setFrom([\Yii::$app->params['supportEmail'] => 'Интернет-магазин ' . \Yii::$app->params['siteName'] . '.kz'])
                            ->setTo($suser_for_yandex)
                            ->setSubject('Яндекс доставка на сайте ' . \Yii::$app->params['siteName'] . '.kz')->send();
                    }
                }

                if($order->payment==2){
                    \Yii::$app->session->set($session_id .'success_order_pay', $order_id);
                    $sum_real=$order_model->realSum();
                    $publicId=Yii::$app->params['cloudpayments']['public_id'];
					$payment = true;
                }else{
                 
                    /**
                     * @var $mailer \yii\swiftmailer\Message
                     */
                    $send_mails = explode(',', \Yii::$app->settings->get('manager_emails', 'viktor@instinct.kz'));
                    foreach ($send_mails as $key_email => &$value_email) {
                        if (!($value_email = trim($value_email, " \t\n\r\0\x0B"))) {
                            unset($send_mails[$key_email]);
                        }
                    }
  
                    if (!empty($this->our_stories_id)) {
                        $pickpoint = Pickpoint::find()->where(['id' => $this->our_stories_id])->one();
                    }

                    \Yii::$app->mailer->compose(['html' => 'admin/order'], ['order' => $order_model, 'delivery' => ($this->type_delivery == 0 ? 'Самовывоз' : 'Доставка'), 'name_pickup' => (!empty($pickpoint) ? $pickpoint->name : '')])
                        ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->params['siteName'] . ' info'])
                        ->setTo($send_mails)
                        ->setSubject('Новый заказ на сайте ' . \Yii::$app->params['siteName'])->send();
                    // if ($data['user_mail']) {
                        // \Yii::$app->mailer->compose(['html' => 'order'], ['order' => $order_model, 'delivery' => ($this->type_delivery == 0 ? 'Самовывоз' : 'Доставка'), 'name_pickup' => (!empty($pickpoint) ? $pickpoint->name : '')])
                            // ->setFrom([\Yii::$app->params['supportEmail'] => 'Интернет-магазин ' . \Yii::$app->params['siteName'] . '.kz'])
                            // ->setTo($data['user_mail'])
                            // ->setSubject('Заказ на сайте ' . \Yii::$app->params['siteName'] . '.kz')->send();
                    // }
                }

                // На всякий случай сделаем проверку на 0
                //if($data['bonus_add'] AND $data['bonus_add'] > 0){
                if(0){
                    // Метод start() возвращает строку, но тут она не используется.
                    Yii::$app->one_signal->start('bonus_add', [
                        'user_ids' => [$data['user_id']],
                        'header' => 'Бонусы за заказ',
                        'message' => 'Вам начислено ' . $data['bonus_add'] . ' бонусов!',
                    ]);
                }

				Yii::$app->session->remove($session_id);
				Yii::$app->session->remove($session_id .'type_handling');
				Yii::$app->session->remove($session_id .'sum');
				Yii::$app->session->remove($session_id .'sets');

                // create order in RetailCRM
                if(\Yii::$app->params['RetailCRM']['enable'] === true){
                    $crmOrder = new CreateCrmOrder();
                    $crmOrder->prepare($order_model);
                    $crmApiHelper = new ApiHelper();
                    $crmApiHelper->createOrder($crmOrder->attributes);
                }

                \Yii::$app->session->set($session_id .'success_order', $order_id);
				
				$result['message'] = 'Успешно';
				$result['success'] = true;				
				$result['order_id'] = $order_id;
				$result['payment_proceed'] = $payment;
			//	$result['message']['paymentLink'] = "https://test.kingfisher.kz/pay.html/?id=$order_id";

                $transaction->commit();
            } else {
                $result['success'] = false;
				$result['message'] = 'Произошла ошибка на стороне сервера!';
             //   $result['error'] = 'Произошла ошибка на стороне сервера!';
				$result['payment_proceed'] = $payment;
                return $result;
            }
        } else {
            $transaction->rollBack();
        }
        return $result;
    }
}
