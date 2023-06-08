<?php

namespace common\models;

use shadow\plugins\datetimepicker\DateTimePicker;
use shadow\helpers\StringHelper;
use shadow\widgets\CKEditor;
use Yii;
use yii\helpers\Inflector;

/**
 * This is the model class for table "promo_code".
 *
 * @property integer $id
 * @property string $code
 * @property string $discount
 * @property string $body
 * @property integer $isEnable
 * @property integer $date_start
 * @property integer $date_end
 * @property string $type
 *
 * @property Orders[] $orders
 */
class PromoCode extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'promo_code';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'discount', 'date_start', 'date_end', 'type', 'device'], 'required'],
            [['body'], 'string'],
            ['code','unique'],
            [['date_start', 'date_end'], 'match', 'pattern' => "/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/i"],
            [['date_start'], 'date', 'timestampAttribute' => 'date_start', 'format' => 'php:d/m/Y'],
            [['date_end'], 'date', 'timestampAttribute' => 'date_end', 'format' => 'php:d/m/Y'],
            [['isEnable'], 'integer'],
            [['code', 'discount'], 'string', 'max' => 255],
            [['min_amount'], 'string', 'max' => 20],
            [['type'], 'string', 'max' => 50],
            [['device'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Код',
            'discount' => 'Скидка',
            'min_amount' => 'Минимально итоговая сумма заказа, которая должна быть в корзине, чтобы промокод активировался.',
            'body' => 'Текст для писем',
            'isEnable' => 'Включён',
            'date_start' => 'Дата начала',
            'date_end' => 'Дата окончания',
            'type' => 'Вид',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['promo_code_id' => 'id']);
    }
    public $data_types = [
        'one' => 'Разовый',
        'many' => 'Многоразовый',
        'reg' => 'За регистрацию',
        'sub' => 'Подписка',
        'first_order_app' => 'Первый заказ из приложения'
    ];
    public $data_types_devices = [
        'web' => 'Все устройства',
        'android' => 'Только для андроид',
        'ios' => 'Только для iOS'
    ];
    public function FormParams()
    {
        if ($this->isNewRecord) {
            $this->loadDefaultValues(true);
            $this->code=StringHelper::num2alpha(time());
            $this->body = '<p>Дарим вам промокод - {code}</p><p>Действителен до {date_end}</p>';
        } else {
            $this->date_start = date('d/m/Y', $this->date_start);
            $this->date_end = date('d/m/Y', $this->date_end);
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
                        'isEnable' => [
                            'type' => 'checkbox'
                        ],
                        'type' => [
                            'type' => 'dropDownList',
                            'data' => $this->data_types,
                            'field_options' => [
                                'inputOptions' => [
                                    'data-type' => 'promo-type'
                                ]
                            ]
                        ],
                        'device' => [
                            'title' => 'Вид устройства',
                            'type' => 'dropDownList',
                            'data' => $this->data_types_devices,
                            'field_options' => [
                                'inputOptions'=>[
                                    // Тут указываем все атрибуты тега
                                    'title' => 'Минимальная сумма заказа для промокода',
                                    'disabled' => 'disabled',
                                    'data-type' => 'promo-type-devices'
                                ]
                            ]
                        ],
                        'code' => [],
                        'discount' => [],
                        'min_amount' => [
                            'title' => 'Минимальная сумма',
                            'field_options' => [
                                'inputOptions'=>[
                                    'placeholder' => 'Минимальная сумма заказа для промокода',
                                    'title' => 'Минимальная сумма заказа для промокода',
                                ]
                            ]
                        ],
                        'date_start' => [
                            'widget' => [
                                'class' => DateTimePicker::className(),
                                'config' => [
                                    'language' => 'ru',
                                    'size' => 'ms',
                                    'template' => '{input}',
                                    'pickButtonIcon' => 'glyphicon glyphicon-time',
                                    'clientOptions' => [
                                        'format' => 'dd/mm/yyyy',
                                        'minView' => 2,
                                        'autoclose' => true,
                                        'todayBtn' => true
                                    ],
                                    'clientEvents' => [
                                        'changeDate' => <<<JS
                                        function(e){
                                        $('#promocode-date_end').datetimepicker('setStartDate', e.date);
                                        }
JS
                                    ]
                                ]
                            ]
                        ],
                        'date_end' => [
                            'widget' => [
                                'class' => DateTimePicker::className(),
                                'config' => [
                                    'language' => 'ru',
                                    'size' => 'ms',
                                    'template' => '{input}',
                                    'pickButtonIcon' => 'glyphicon glyphicon-time',
                                    'clientOptions' => [
                                        'format' => 'dd/mm/yyyy',
                                        'minView' => 2,
                                        'autoclose' => true,
                                        'todayBtn' => true
                                    ]
                                ]
                            ]
                        ],
                        'body' => [
                            'type' => 'textArea',
                            'widget' => [
                                'class' => CKEditor::className(),
                                'config' => [
                                    'editorOptions' => [
                                        'enterMode' => 1
                                    ]
                                ]
                            ]
                        ],
                    ],
                ]
            ]
        ];

        $view = Yii::$app->view;
        $view->registerJs(<<<JS
        var promo_type = $('[data-type=promo-type]'),
            promo_type_devices = $('[data-type=promo-type-devices]');

promo_type.on('change', function(){
    if ($(this).val() == 'first_order_app') {
        promo_type_devices.prop('disabled', false);
    } else {
        promo_type_devices.prop('disabled', true).val('web');
    }
});
JS
        );

        return $result;
    }
    public function discount($price)
    {
        $discount = preg_replace("#([^-\d%]*)#u", '', $this->discount);
        if ($discount) {
            if (preg_match("#\%$#u", $discount)) {
                $discount = preg_replace("#\%$#u", '', $discount);
                $price = round(((double)$price * (double)$discount) / 100);
            } else {
                $price = $discount;
            }
        } else {
            $price = 0;
        }
        return $price;
    } 
    public function check_enable()
    {
        if($this->isEnable){
            $time = time();
            $end_day = strtotime(date('d.m.Y', $this->date_end).' 23:59:59');
            $start_day=strtotime(date('d.m.Y', $this->date_start).' 00:00:00');
            if($time>$start_day&&$time<$end_day){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
