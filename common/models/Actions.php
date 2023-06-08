<?php
namespace common\models;

use common\components\Debugger as d;
use shadow\plugins\datetimepicker\DateTimePicker;
use shadow\assets\Select2Assets;
use shadow\widgets\CKEditor;
use Yii;
use yii\helpers\Inflector;
use shadow\multilingual\behaviors\MultilingualBehavior;
use shadow\multilingual\behaviors\MultilingualQuery;
use shadow\plugins\seo\behaviors\SSeoBehavior;
use shadow\SResizeImg;

/**
 * This is the model class for table "actions".
 *
 * @property integer $id
 * @property string $name
 * @property string $small_body
 * @property string $body
 * @property string $img
 * @property integer $created_at
 * @property integer $date_start
 * @property integer $date_end
 * @property integer $isVisible
 *
 * @property ActionsItems[] $actionsItems
 * @property Items[] $items
 */
class Actions extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'actions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'body', 'created_at', 'date_start', 'date_end'], 'required'],
            [['created_at', 'date_start', 'date_end'], 'match', 'pattern' => "/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/i"],
            [['created_at'], 'date', 'timestampAttribute' => 'created_at', 'format' => 'php:d/m/Y'],
            [['date_start'], 'date', 'timestampAttribute' => 'date_start', 'format' => 'php:d/m/Y'],
            [['date_end'], 'date', 'timestampAttribute' => 'date_end', 'format' => 'php:d/m/Y'],
            [['body'], 'string'],
            [['isVisible'], 'default', 'value' => 1],
            [['isWholesale'], 'default', 'value' => 0],
            [['isVisible', 'isWholesale'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [
				'img', 'image',
				'extensions' => 'jpg, gif, png, jpeg',
//				'skipOnEmpty' => !$this->isNewRecord
			],
            [['small_body'], 'string', 'max' => 1000],
            [['items'], 'safe'],
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
            'small_body' => 'Краткий текст',
            'body' => 'Текст',
            'img' => 'Изображение',
            'created_at' => 'Дата создания',
            'date_start' => 'Дата начала',
            'date_end' => 'Дата окончания',
            'items' => 'Товары',
            'isVisible' => 'Видимость',
            'isWholesale' => 'Видно только оптовикам',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActionsItems()
    {
        return $this->hasMany(ActionsItems::className(), ['action_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Items::className(), ['id' => 'item_id'])->via('actionsItems');
    }
    public function setItems($items)
    {
        if (!is_array($items)) {
            $items = [];
        }
        $event_after = $this->isNewRecord ? $this::EVENT_AFTER_INSERT : $this::EVENT_AFTER_UPDATE;
        $name = 'items';
        $this->on($event_after, function ($event) use ($name, $items) {
            Yii::trace('start saveRelation');
            $this->saveRelation($name, $items, $event);
        });
    }
	
    public $action_name = '';
    public $action_description = '';
    public $onesignal_notification = '';
    public $send_all = '';
    public function FormParams()
    {
        if ($this->isNewRecord) {
            $this->loadDefaultValues(true);
            $this->created_at = date('d/m/Y');
        } else {
            $this->created_at = date('d/m/Y', $this->created_at);
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
                        'isVisible' => [
                            'type' => 'checkbox'
                        ],
                        'isWholesale' => [
                            'type' => 'checkbox'
                        ],
                        'name' => [],
                        'img' => [
                            'type' => 'img'
                        ],
                        'small_body' => [
                            'type' => 'textArea'
                        ],
                        'created_at' => [
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
                                        $('#actions-date_end').datetimepicker('setStartDate', e.date);
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
                ],
                'action_items' => [
                    'title' => 'Товары',
                    'icon' => 'th-list',
                    'options' => [],
                    'fields' => [
                        'items' => [
                            'title' => 'Товары',
                            'type' => 'dropDownList',
                            'data' => Items::find()->select(['name', 'id'])->indexBy('id')->column(),
                            'params' => [
                                'multiple' => true,
                            ]
                        ],
                    ]
                ]
            ]
        ];

        if(!$this->isNewRecord){
            $one_signal = [
                'one_signal' => [
                    'title' => 'Пуш уведомления',
                    'icon' => 'paper-plane',
                    'options' => [],
                    'fields' => [
                        'action_name' => [
                            'title' => 'Заголовок',
                            'field_options' => [
                                'inputOptions' => [
                                    'placeholder' => 'Название акции',
                                    'value' => $this->name
                                ]
                            ]
                        ],
                        'action_description' => [
                            'type' => 'textArea',
                            'title' => 'Описание',
                            'field_options' => [
                                'inputOptions' => [
                                    'placeholder' => 'Описание акции'
                                ]
                            ]
                        ],
                        'send_all' => [
                            'type' => 'checkbox',
                            'title' => 'Отправить всем пользователям',
                            // Что то это не отображается, надо разобраться потом.
//                            'field_options' => [
//                                'options' => [
//                                    'class' => 'hahaha'
//                                ],
//                                'inputOptions' => [
//                                    'title' => 'Отправить всем зарегистрированным пользователям',
//                                    'template' => '<label>{input}</label>'
//                                ],
//                            ],
                        ],
                        'onesignal_notification' => [
                            'type' => 'submintButton',
                            'title' => 'Отправить уведомление в приложение',
                            'icon' => 'paper-plane',
                            'buttonOptions' => [
                                'type' => 'submit',
                                'class' => 'btn-onesignal',
                                'name' => 'onesignal_notification',
                                'value' => 'action',
                            ]
                        ],
                        'action_id' => [
                            'type' => 'hidden',
                            'title' => '',
                            'field_options' => [
                                'inputOptions' => [
                                    'value' => $this->id
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            $result['groups'] = array_merge($result['groups'], $one_signal);
        }


        $form_name = strtolower($this->formName());
        $view = Yii::$app->view;
        Select2Assets::register($view);
        $view->registerJs(<<<JS
$('#{$form_name}-items').select2({
    width: '100%',
    language: 'ru'
});
JS
        );
        return $result;
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $result = [
            [
                'class' => '\shadow\behaviors\UploadFileBehavior',
                'attributes' => ['img'],
            ],
        ];
        if (SSeoBehavior::enableSeoEdit()) {
            $result['seo'] = [
                'class' => SSeoBehavior::className(),
                'nameTranslate' => 'name',
                'controller' => 'site',
                'action' => 'actions',
            ];
        }
        return $result;
    }
    public static function find()
    {
        if (Yii::$app->function_system->enable_multi_lang()) {
            $q = new MultilingualQuery(get_called_class());
            if (Yii::$app->id == 'app-backend') {
                $q->multilingual();
            } else {
                $q->localized();
            }
            return $q;
        } else {
            $q = parent::find();
        }
        if (SSeoBehavior::enableSeoEdit()) {
            SSeoBehavior::modificationSeoQuery($q);
        }
        if(Yii::$app->id== 'app-frontend'){
//            $q->andWhere(['`actions`.`isDeleted`' => 0]);
        }
        return $q;
    }
    use SResizeImg;
    public function img($resize = false, $size_type = 'mini', $array = false)
    {
        if (!$array) {
            if ($this->img) {
                if ($resize && isset(ItemImg::$_size_img_a[$size_type])) {
                    $result = $this->resizeImg(ItemImg::$_size_img_a[$size_type], 'img');
                } else {
                    $result = $this->img;
                }
            } else {
                if (isset($this->itemImgs[0]->url)) {
                    if ($resize) {
                        $result = $this->itemImgs[0]->resizeImg($size_type);
                    } else {
                        $result = $this->itemImgs[0]->url;
                    }
                } else {
                    $result = '/uploads/no_photo.png';
                }
            }
            if (!$result) {
                $result = '/uploads/no_photo.png';
            }
            if ($result != '/uploads/no_photo.png' && !is_file(Yii::getAlias('@frontend/web') . $result)) {
                $result = '/uploads/no_photo.png';
            }
        } else {
            $result = [];
            if ($this->itemImgs) {
                $result = array();
                foreach ($this->itemImgs as $img) {
                    if ($resize) {
                        if (is_array($size_type)) {
                            $img_size = [];
                            foreach ($size_type as $value) {
                                if ($img_resize = $img->resizeImg($value)) {
                                    $img_size[$value] = $img_resize;
                                }else{
                                    $img_size[$value] = '';
                                }
                            }
                            $img_size['title'] = $img->name;
                            $result[] = $img_size;
                        } else {
                            if ($img_resize = $img->resizeImg($size_type)) {
                                $result[] = $img_resize;
                            }
                        }
                    } else {
                        if (is_file(Yii::getAlias('@frontend/web') . $img->url)) {
                            $result[] = $img->url;
                        }
                    }
                }
            } else {
                if ($this->img) {
                    if ($resize) {
                        if (is_array($size_type)) {
                            $img_size = [];
                            foreach ($size_type as $value) {
                                if (!isset(ItemImg::$_size_img_a[$value])) {
                                    if (is_file(Yii::getAlias('@frontend/web') . $this->img)) {
                                        $img_size[$value] = $this->img;
                                    }else{
                                        $img_size[$value] = null;
                                    }
                                    continue;
                                }
                                if ($img_resize = $this->resizeImg(ItemImg::$_size_img_a[$value], 'img')) {
                                    $img_size[$value] = $img_resize;
                                }
                            }
                            $img_size['title'] = $this->name;
                            $result[] = $img_size;
                        } else {
                            if (isset(ItemImg::$_size_img_a[$size_type]) && ($img_resize = $this->resizeImg(ItemImg::$_size_img_a[$size_type], 'img'))) {
                                $result[] = $img_resize;
                            }
                        }
                    } else {
                        if (is_file(Yii::getAlias('@frontend/web') . $this->img)) {
                            $result[] = $this->img;
                        }
                    }
                }
            }
            if (!$result) {
                $result[] = '/uploads/no_photo.png';
            }
        }
        return $result;
    }
    /**
     * 16 мая - 1 июня 2015
     */
    public function rang_date()
    {
        $start_string=date('j',$this->date_start);
        $day_start=date('j',$this->date_start);
        $day_end=date('j',$this->date_end);
        $year_start=date('Y',$this->date_start);
        $year_end=date('Y',$this->date_end);
        $mont_start = date('m',$this->date_start);
        $mont_end = date('m',$this->date_end);
        if($mont_start!=$mont_end||$year_start!=$year_end){
            $start_string.=' '.Yii::$app->formatter->asDate($this->date_start, 'MMMM');
            if($year_start!=$year_end){
                $start_string .= ' ' . $year_start;
            }
        }
        $end_string = '';
        if($day_start!=$day_end){
            $end_string .= ' - ';
            $end_string .= date('j', $this->date_end);
        }
        $end_string .= ' ' . Yii::$app->formatter->asDate($this->date_end, 'MMMM').' '.$year_end;
        return $start_string . $end_string;
    }
}
