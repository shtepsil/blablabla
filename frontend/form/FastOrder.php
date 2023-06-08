<?php
/**
 * Created by PhpStorm.
 * Project: kingfisher
 * User: lxShaDoWxl
 * Date: 13.10.15
 * Time: 9:56
 */
namespace frontend\form;

use common\components\retailcrm\ApiHelper;
use common\models\Items;
use common\models\Orders;
use common\models\OrdersHistory;
use common\models\OrdersItems;
use common\models\Sets;
use common\models\User;
use frontend\models\retailcrm\CreateCrmOrder;
use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use yii\helpers\Url;

class FastOrder extends Model
{
    public $name;
    public $phone;
    public $items;
    public $type;

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
        return 'fast_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
            [['name'], 'required', 'on' => ['popup']],
            [['name', 'phone'], 'string', 'max' => 255],
            [['phone'],'match','pattern' => '/^((\+?7)(\(?\d{3})\)-?)?(\d{3})(-?\d{4})$/','message'=>Yii::t('main','Некорректный формат поля {attribute}')],
            [['name', 'phone', 'type', 'items'], 'safe'],
        ];
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
        if ($this->type != 2) {
            $this->scenario = 'popup';
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'ФИО',
            'phone' => 'Телефон',
        ];
    }
    public function send()
    {
        /**
         * @var $user \common\models\User
         */
        $result = [];
        $data = [];
        $connect = \Yii::$app->db;
        $transaction = $connect->beginTransaction();
        if (\Yii::$app->user->isGuest) {
            $user = User::checkPhone($this->phone,[
                'username'=>($this->name ? $this->name : 'Быстрый заказ'),
                'isEntity'=>0,
            ]);
        } else {
            $user = \Yii::$app->user->identity;
        }
        $time = time();
        $data = [
            'bonus_use' => 0,
            'payment' => 0,
            'isFast' => 1,
            'status' => 0,
            'created_at' => $time,
            'updated_at' => $time,
        ];
        if (!$user) {
            $data['user_name'] = ($this->name ? $this->name : 'Быстрый заказ');
            $data['user_phone'] = $this->phone;
//            $data['user_mail'] = $this->email;
//            $data['isEntity'] = $this->isEntity;
            $data['user_address'] = 'Не указан';
        } else {
            $data['user_id'] = $user->id;
            $data['user_name'] = ($this->name ? $this->name : $user->username);
            $data['user_phone'] = $this->phone;
            $data['user_mail'] = $user->email;
            $data['isEntity'] = $user->isEntity;
            $data['bonus_use'] = 0;
            $data['user_address'] = 'Не указан';
        }
        $data['date_delivery'] = $time;
        $data['time_delivery'] = 'Не указано';
        /**
         * @var $items Items[]
         */
        if($this->type==1) {//Обычный товар
            $sessions_sets = $sessions_type_handling = [];
            $sessions_items[$this->items] = 1;
        }elseif ($this->type == 2) {//корзина
            $sessions_items = Yii::$app->session->get('items', []);
            $sessions_type_handling = Yii::$app->session->get('type_handling', []);
            $sessions_sets = Yii::$app->session->get('sets', []);
        } elseif($this->type==3) {//сет
            $sessions_items = $sessions_type_handling = [];
            $sessions_sets[$this->items] = 1;
        }elseif($this->type==4) {//"купить вместе"
            $sessions_sets = $sessions_type_handling = [];
            $sessions_items[$this->items] = 1;
            /**
             * @var $item_add Items
             */
            $item_add = Items::findOne($this->items);
            if ($item_add && $item_add->isVisible && $item_add->itemsTogethers) {
                foreach ($item_add->itemsTogethers as $items_together) {
                    $sessions_items[$items_together->item_id] = 1;
                }
            }else{
                $result['message']['error'] = 'Произошла ошибка на стороне сервера!';
            }
        }else{
            $result['message']['error'] = 'Произошла ошибка на стороне сервера!';
            return $result;
        }
        $sum = 0;
        $full_bonus = 0;
        $full_purch_price = 0;
        $data_items = $insert_handing = $data_sets = [];
        $order = new Orders();
        if ($sessions_items) {
            $q = new ActiveQuery(Items::className());
            /**
             * @var $functions \frontend\components\FunctionComponent
             */
            $functions = Yii::$app->function_system;
            $q->indexBy('id')
                ->with('itemsTogethers')
                ->andWhere(['id' => array_keys($sessions_items)]);
            $items = $q->all();
            $discount = $functions->discount_sale_items($items, $sessions_items);
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
                    'weight' => (($item->measure == 0 || ($item->measure != $item->measure_price)) ? ($count * $item->weight) : 0),
                    'purch_price' => $item->purch_price,
                    'bonus_manager' => $bonus,
                    'data' => Json::encode($order->convert_to_array($item))
                ];
                $sum += $functions->full_item_price($discount, $item, $count);;
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
        $percent_bonus = Yii::$app->function_system->percent();
        $full_bonus = floor(((int)$sum * ($percent_bonus)) / 100);
        $data['bonus_add'] = $full_bonus;
        if ($connect->createCommand()->insert('orders', $data)->execute() && ($data_items || $data_sets)) {
            $order_id = $connect->getLastInsertID();
            $data['id'] = $order_id;
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
                $url = Url::to(['site/success-order']);

                $result['message']['success'] = 'Ваш заказ успешно отпрален!';
                $history = new OrdersHistory();
                $history->user_name = $data['user_name'];
                $history->order_id = $order_id;
                $history->action = 1;
                $history->save(false);

                if (\Yii::$app->params['RetailCRM']['enable'] === true) {
                    // create order in RetailCRM
                    $order_model = Orders::findOne($order_id);
                    $crmOrder = new CreateCrmOrder();
                    $crmOrder->prepare($order_model);
                    $crmApiHelper = new ApiHelper();
                    $crmApiHelper->createOrder($crmOrder->attributes);
                }

                if ($this->type == 2) {
                    $result['js'] = <<<JS
window.location='{$url}'
JS;
                    \Yii::$app->session->set('success_order', true);
                    Yii::$app->session->remove('items');
                    Yii::$app->session->remove('type_handling');
                    Yii::$app->session->remove('sets');
                } else {
                    $result['js'] = <<<JS
window.location='{$url}'
JS;
                }
                \Yii::$app->session->set('success_order', $order_id);

                $transaction->commit();
                /**
                 * @var $mailer \yii\swiftmailer\Message
                 */
                $send_mails = explode(',', \Yii::$app->settings->get('manager_emails', 'viktor@instinct.kz'));
                foreach ($send_mails as $key_email => &$value_email) {
                    if (!($value_email = trim($value_email, " \t\n\r\0\x0B"))) {
                        unset($send_mails[$key_email]);
                    }
                }
                $data_email = [
                    'order' => new Orders($data),
                ];
                \Yii::$app->mailer->compose(['html' => 'admin/order'], $data_email)
                    ->setFrom([\Yii::$app->params['supportEmail'] => 'Интернет-магазин ' . \Yii::$app->params['siteName'] . '.kz'])
                    ->setTo($send_mails)
                    ->setSubject('Быстрый заказ на сайте ' . \Yii::$app->params['siteName'])->send();
                if (isset($data['user_mail']) && $data['user_mail']) {
                    \Yii::$app->mailer->compose(['html' => 'order'], ['order' => Orders::findOne($order_id)])
                        ->setFrom([\Yii::$app->params['supportEmail'] => 'Интернет-магазин ' . \Yii::$app->params['siteName'] . '.kz'])
                        ->setTo($data['user_mail'])
                        ->setSubject('Быстрый заказ на сайте ' . \Yii::$app->params['siteName'] . '.kz')->send();
                }
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
