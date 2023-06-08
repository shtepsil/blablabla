<?php
/**
 * Created by PhpStorm.
 * Project: kingfisher
 * User: lxShaDoWxl
 * Date: 10.11.15
 * Time: 15:08
 */
namespace frontend\form;

use common\models\Items;
use common\models\OrdersHistory;
use common\models\OrdersItems;
use common\models\Sets;
use yii\base\Model;
use yii\db\ActiveQuery;

class ReplayOrder extends Model
{
    public $user_name;
    public $phone;
    public $address;
    public $city;
    public $payment;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'phone'], 'trim'],
            [['phone'], 'match', 'pattern' => '/^((\+?7)(\(?\d{3})\)-?)?(\d{3})(-?\d{4})$/', 'message' => \Yii::t('main', 'Некорректный формат поля {attribute}')],
            [['user_name', 'phone', 'address', 'payment', 'city'], 'required'],
            [['city'], 'integer'],
            [['address'], 'string', 'max' => 255],
            [['city', 'user_name', 'address', 'phone', 'payment'], 'safe'],
        ];
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_name' => 'Имя, фамилия получателя',
            'phone' => 'Телефон',
            'address' => 'Адрес доставки',
            'city' => 'Город',
            'payment' => 'Способ оплаты',
        ];
    }

    public $data_payment = [
        1 => 'Наличные курьеру',
        2 => 'Банковская карта (Visa/Mastercard)',
    ];
    public function send()
    {
        /**
         * @var $user \common\models\User
         */
        $result = [];
        $data = [];
        $connect = \Yii::$app->db;
        $transaction = $connect->beginTransaction();
        $user = \Yii::$app->user->identity;
        $time = time();
        $data = [
            'user_comments' => '',
            'code' => '',
            'bonus_use' => 0,
            'payment' => $this->payment,
            'status' => 0,
            'city_id' => $this->city,
            'created_at' => $time,
            'updated_at' => $time,
        ];
        $data['user_id'] = $user->id;
        $data['user_name'] = $this->user_name;
        $data['user_phone'] = $this->phone;
        $data['user_mail'] = $user->email;
        $data['isEntity'] = $user->isEntity;
        $data['user_address'] = 'г.' . \Yii::$app->function_system->data_city[$this->city] . ', ' . $this->address;
        if (doubleval($user->discount)) {
            $data['discount'] = $user->discount . '%';
        }
        $data['isWholesale'] = $user->isWholesale;
        $data['date_delivery'] = $time;
        $data['time_delivery'] = '';
        /**
         * @var $items Items[]
         */
        $sessions_items = \Yii::$app->request->post('items', []);
        $sessions_type_handling = \Yii::$app->request->post('type_handling', []);
        $sessions_sets = \ Yii::$app->request->post('sets', []);
        if (!$sessions_sets && !$sessions_items) {
            $result['js'] = <<<JS
$.growl.error({title:'Ошибка', message: 'Заказ пустой!' ,duration:5000});
JS;
            return $result;
        }
        $sum = 0;
        $full_purch_price = 0;
        $data_items = $data_sets = $insert_handing = [];
        if ($sessions_items) {
            $q = new ActiveQuery(Items::className());
            $q->indexBy('id')
                ->andWhere(['id' => array_keys($sessions_items)]);
            $items = $q->all();
            foreach ($items as $key => $item) {
                $count = $sessions_items[$key];
                $handling = [];
                if (isset($sessions_type_handling[$item->id])) {
                    $handling = $sessions_type_handling[$item->id];
                }
                $bonus = $item->price_bonus_manager();
                $data_items[] = [
                    'order_id' => '{order_id}',
                    'item_id' => $item->id,
                    'count' => $count,
                    'price' => $item->real_price(),
                    'purch_price' => $item->purch_price,
                    'bonus_manager' => $bonus
                ];
                $sum += $item->sum_price($count);
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
        $data['full_price'] = $sum;
        $percent_bonus = \Yii::$app->function_system->percent();
        $full_bonus = floor(((int)$sum * ($percent_bonus)) / 100);
        $data['bonus_add'] = $full_bonus;
        if ($connect->createCommand()->insert('orders', $data)->execute() && ($data_items || $data_sets)) {
            \Yii::$app->session->remove('invited_code');
            $order_id = $connect->getLastInsertID();
            $send = false;
            if ($data_items) {
                foreach ($data_items as &$order_item) {
                    $order_item['order_id'] = $order_id;
                }
                if ($connect->createCommand()
                    ->batchInsert('orders_items', ['order_id', 'item_id', 'count', 'price', 'purch_price', 'bonus_manager'], $data_items)
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
                            \Yii::$app->db->createCommand()->batchInsert('orders_items_handing', [
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
                $result['message']['success'] = 'Ваш заказ успешно отпрален!';
                $history = new OrdersHistory();
                $history->user_name = $data['user_name'];
                $history->order_id = $order_id;
                $history->action = 1;
                $history->save(false);
                $result['js'] = <<<JS
window.location='/'
JS;
                \Yii::$app->session->set('success_order', true);
                $transaction->commit();
            } else {
                $result['message']['error'] = 'Произошла ошибка на стороне сервера!';
                return $result;
            }
        } else {
            $transaction->rollBack();
        }
        return $result;
    }
}