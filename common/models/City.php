<?php

namespace common\models;

use common\components\Debugger as d;
use shadow\widgets\CKEditor;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;
use backend\models\Settings;
use yii\helpers\Url;

/**
 * This is the model class for table "city".
 *
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property integer $price_delivery
 * @property string $info_delivery
 * @property string $pickup
 * @property integer $isOnlyPickup
 * @property integer $not_delete При наличии запись не удаляемая
 * @property string $payment_type
 * @property float $delivery_weight_sum
 * @property float $delivery_free_sum
 *
 * @property ItemsCount[] $itemsCounts
 */
class City extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name','phone'], 'string', 'max' => 255],
            [['pickup','info_delivery', 'coordinate'], 'string'],
            ['not_delete', 'default', 'value' => false],
            ['isOnlyPickup', 'default', 'value' => false],
			['isYandexDelivery', 'default', 'value' => false],
			['pickup_switcher', 'default', 'value' => false],
            [['price_delivery','isOnlyPickup', 'min_order_amount_retail', 'min_order_amount_opt'], 'integer'],
            [['delivery_weight_sum', 'delivery_free_sum', 'pickup_price', 'view'], 'integer'],
            ['payment_type', 'jsonType'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'phone' => 'Телефон',
            'price_delivery' => 'Стоимость доставки (общая)',
            'delivery_weight_sum' => 'Стоимость доставки за 1 кг',
            'delivery_free_sum' => 'Бесплатная доставка курьером при стоимости от',
            'payment_type' => 'Способы оплаты',
            'info_delivery' => 'Информация о доставки',
            'pickup' => 'Место самовывоза',
            'isOnlyPickup' => 'Только самовывоз',
            'not_delete' => 'Не удаляемый',
            'coordinate' => 'Координаты',
			'isYandexDelivery' => 'Есть Яндекс доставка',
            'pickup_price' => 'Стоимость самовывоза',
            'min_order_amount_retail' => 'Минимальная сумма для розничного заказа',
            'min_order_amount_opt' => 'Минимальная сумма для оптового заказа'
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->payment_type = ($this->payment_type ? Json::encode($this->payment_type) : Json::encode([]));

            return true;
        }

        return false;
    }

    public function afterFind(){
        parent::afterFind();

        $this->payment_type = ($this->payment_type ? Json::decode($this->payment_type) : []);
    }

    public function jsonType($attribute, $params)
    {
        if (!empty($this->payment_type) && !Json::encode($this->payment_type)) {
            $this->addError($attribute, 'Wrong json text.');
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemsCounts()
    {
        return $this->hasMany(ItemsCount::className(), ['city_id' => 'id']);
    }

    /**
     * $min_order_amount будет либо число либо NULL
     * @param $city_id
     * @return int|mixed|null
     */
    public static function getMinOrderAmount($city_id){
        $min_order_amount = 0;
        if($current_city = City::findOne($city_id) AND User::$current_user){
            $min_order_amount = $current_city->min_order_amount_retail;
            if(User::$current_user->isWholesale()){
                $min_order_amount = $current_city->min_order_amount_opt;
            }
        }
        return $min_order_amount;
    }

    public function FormParams()
    {
        if ($this->isNewRecord) {
            $this->loadDefaultValues(true);
        }

        $controller_name = Inflector::camel2id(Yii::$app->controller->id);

        $result = [
            'form_action' => ["$controller_name/save"],
            'cancel' => ["$controller_name/index"],
            'groups' => [
                'main' => [
                    'title' => 'Основное',
                    'icon' => 'suitcase',
                    'options' => [],
                    'fields' => [
                        'name' => [],
                        'phone' => [],
                        'coordinate' => []
                    ]
                ],
                'info' => [
                    'title' => 'Информация о доставке',
                    'icon' => 'file-text',
                    'options' => [],
                    'fields' => [
                        'info_delivery' => [
                            'type' => 'textArea',
                            'widget' => [
                                'class' => CKEditor::className(),
                            ]
                        ],
                        'pickup' => [
                            'type' => 'textArea',
                            'widget' => [
                                'class' => CKEditor::className(),
                            ]
                        ]
                    ]
                ],
                'cost' => [
                    'title' => 'Стоимость доставки',
                    'icon' => 'truck',
                    'options' => [],
                    'fields' => [
                        'price_delivery' => [],
                        'delivery_weight_sum' => [],
                        'delivery_free_sum' => [],
                        'pickup_price' => [],
                        'isOnlyPickup' => [
                            'title' => 'Отключить "Доставку курьером" и оставить самовывоз',
                            'type' => 'checkbox'
                        ],
                        'pickup_switcher' => [
                            'title' => 'Самовывоз вкл/откл',
                            'type' => 'checkbox'
                        ],
						'isYandexDelivery' => [
                            'type' => 'checkbox'
                        ],
                    ]
                ],
                'payment' => [
                    'title' => 'Типы оплаты',
                    'icon' => 'money',
                    'options' => [],
                    'fields' => [
                        'payment_type' => [
                            'type' => 'checkboxList',
                            'data' => ArrayHelper::map(
                                Settings::find()->where(['group' => 'payment_type'])->select(['key', 'value'])->all(), 'key', 'value'
                            ),
                        ]
                    ]
                ],
                'min_amount_order' => [
                    'title' => 'Минимальная сумма заказа',
                    'icon' => 'sort-amount-desc',
                    'options' => [],
                    'fields' => [
                        'min_order_amount_retail' => [
                            'field_options' => [
                                'inputOptions'=>[
                                    'placeholder' => '0',
                                ]
                            ]
                        ],
                        'min_order_amount_opt' => [
                            'field_options' => [
                                'inputOptions'=>[
                                    'placeholder' => '0',
                                ]
                            ]
                        ],
                    ]
                ]
            ]
        ];
        return $result;
    }

    public function url(){
        $params = [
            'site/index',
            'city' => $this->id,
        ];

        return Url::to($params);
    }
	
}//Class
