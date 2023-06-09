<?php
/**
 * Created by PhpStorm.
 * Project: kingfisher
 * User: lxShaDoWxl
 * Date: 31.10.15
 * Time: 17:07
 */
namespace backend\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
/**
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property integer $owner_id
 * @property string $url
 * @property integer $isVisible
 * @property integer $sort
 * @property integer $parent_id
 *
 * @property BaseMenu $parent
 * @property BaseMenu[] $menus
 */
class BaseMenu extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['owner_id', 'isVisible', 'sort', 'parent_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 50],
            [['url'], 'string', 'max' => 500],
            [['module', 'page'], 'integer'],
            [['module', 'page'], 'safe'],
            [['module'], 'required', 'on' => ['module']],
            [['page'], 'required', 'on' => ['page']],
		//	[['img'], 'image', 'extensions' => ['jpg', 'gif', 'png', 'jpeg']],
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
            'type' => 'Тип',
            'owner_id' => 'Owner ID',
            'url' => 'Ссылка',
            'isVisible' => 'Видимость',
            'sort' => 'Порядок',
            'parent_id' => 'Родитель',
            'module' => 'Модуль',
            'page' => 'Страница',
			'img' => 'Картинка',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne($this::className(), ['id' => 'parent_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenus()
    {
        return $this->hasMany($this::className(), ['parent_id' => 'id'])->orderBy(['sort' => SORT_ASC]);
    }
    public function beforeValidate()
    {
        if ($this->type) {
            $this->owner_id = $this->{$this->type};
            $this->scenario = $this->type;
        } else {
            $this->owner_id = null;
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }
    public function beforeSave($insert)
    {
        if (!$this->parent_id||$this->no_parent) {
            $this->parent_id = null;
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
    public $module;
    public $page;

    public $data_types = [
        '' => 'Пустое',
        'page' => 'Текстовая страница',
        'module' => 'Модуль'
    ];
    public $no_parent = false;
    public function FormParams()
    {
        $form_name = strtolower($this->formName());
        Yii::$app->getView()->registerJs(<<<JS
$('#{$form_name}-type').on('change',function() {
var val=$(this).val();
  $('.field-{$form_name}-page').hide();
  $('.field-{$form_name}-module').hide();
  $('.field-{$form_name}-'+val).show();
})
JS
        );
        $fields = [
            'isVisible' => [
                'type' => 'checkbox'
            ],
            'name' => [],
            'sort' => [],
            'parent_id' => [
                'relation' => [
                    'class' => $this::className(),
                    'query' => [
                        'where' => ['parent_id' => null]
                    ]
                ]
            ],
            'type' => [
                'type' => 'dropDownList',
                'data' => $this->data_types,
            ],
            'module' => [
                'relation' => [
                    'class' => Module::className(),
                ],
                'field_options' => [
                    'options' => ['style' => ($this->type == 'module') ? '' : 'display:none'],
                ]
            ],
            'page' => [
                'relation' => [
                    'class' => Pages::className(),
                ],
                'field_options' => [
                    'options' => ['style' => ($this->type == 'page') ? '' : 'display:none'],
                ]
            ],
        ];
        if ($this->isNewRecord) {
            $this->loadDefaultValues(true);
        } else {
            if ($this->type) {
                $this->{$this->type} = $this->owner_id;
            }
            if ($this->menus) {
                unset($fields['parent_id']);
            } else {
                $q_patent = $fields['parent_id']['relation']['query']['where'];
                $fields['parent_id']['relation']['query']['where'] = ['and', $q_patent, ['<>', 'id', $this->id]];
            }
        }
        $controller_name = Inflector::camel2id(Yii::$app->controller->id);

        if($this->no_parent){
            unset($fields['parent_id']);
        }
        $result = [
            'form_action' => [$controller_name . '/save'],
            'cancel' => ["$controller_name/index" ],
            'fields' => $fields,
        ];

        return $result;
    }
    public $data_status = array(
        0 => 'Скрыто',
        1 => 'Опубликовано',
    );
    public function status()
    {
        return $this->data_status[$this->isVisible];
    }
    public static function getListItems($model=null)
    {
        /**
         * @var $items self[]
         */
        $controller_name = Inflector::camel2id(Yii::$app->controller->id);
        if($model==null){
            $model = new Menu();
        }
        $result['columns'] = [
            'status' => [
                'function' => function ($item) {
                    return Html::tag('span', $item->status(), ['class' => 'label label-success editable-status editable editable-click', 'data-id' => $item->id]);
                }
            ]
        ];
        $result['controls'] = [
//            'add_parent' => [
//                'url' => ['footer-menu/control', 'parent' => '{id}'],
//                'icon' => 'plus',
//                'options' => [
//                    'class' => 'btn-success btn-xs btn'
//                ]
//            ],
            'deleted' => [
                'url' => ["$controller_name/deleted", 'id' => '{id}'],
                'icon' => 'times fa-inverse',
                'options' => [
                    'class' => 'btn-xs btn-confirm btn-danger'
                ]
            ]
        ];
        $items = $model::find()->orderBy('`sort` ASC')->where(['parent_id' => null])->with('menus')->all();
        foreach ($items as $item) {
            $sub_items = [];
            if ($item->menus) {
                foreach ($item->menus as $sub_item) {
                    $sub_items[$sub_item->id] = [
                        'model' => $sub_item,
                        'link' => [
                            'title' => $sub_item->name,
                            'url' => ["$controller_name/control", 'id' => $sub_item->id],
                        ]
                    ];
                }
            }
            $result['items'][$item->id] = [
                'model' => $item,
                'link' => [
                    'title' => $item->name,
                    'url' => ["$controller_name/control", 'id' => $item->id],
//                    'prev'=>'site/'.$item->url,
                ],
                'items' => $sub_items
            ];
        }
        return $result;
    }
    public function createUrl()
    {
        /**
         * @var $module Module
         */
        $result = '#';
        if ($this->type) {
            switch ($this->type) {
                case 'page':
                    $result = Url::to(['site/page', 'id' => $this->owner_id]);
                    break;
                case 'module':
                    $module = Module::findOne($this->owner_id);
                    if ($module) {
                        $result = Url::to([$module->action]);
                    }
                    break;
            }
        }
        return $result;
    }
}