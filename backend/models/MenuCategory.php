<?php
namespace backend\models;

use common\models\Category;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;

/**
 * This is the model class for table "footer_menu".
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
 * @property MenuCategory $parent
 * @property MenuCategory[] $menus
 * @property Category $cat
 */
class MenuCategory extends Menu
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCat()
    {
        return $this->hasOne(Category::className(), ['id' => 'owner_id'])
            ->onCondition(['`menu_category`.type'=>'category'])
            ->andWhere(['`category`.isVisible'=>1])
            ->with([
                'categories' => function ($q) {
                    $q->where(['isVisible' => 1])->orderBy(['sort' => SORT_ASC]);
                }
            ])
            ->join('LEFT JOIN','menu_category','`category`.`id` = `menu_category`.`owner_id` ')
            ;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu_category';
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
            ]      
        ];

		return $result;
    }
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['category'], 'integer'],
                [['category'], 'safe'],
                [['category'], 'required', 'on' => ['category']],
            ]
        );
    }
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'category' => 'Категория',
            ]
        );
    }
    public static function getListItems($model = null)
    {
        if ($model == null) {
            return parent::getListItems(new MenuCategory());
        } else {
            return parent::getListItems($model);
        }
    }
    public $category;
    public $data_types = [
        '' => 'Пустое',
        'page' => 'Текстовая страница',
        'module' => 'Модуль',
        'category' => 'Категория',
    ];
    public function FormParams()
    {
        $form_name = strtolower($this->formName());
        Yii::$app->getView()->registerJs(<<<JS
$('#{$form_name}-type').on('change',function() {
var val=$(this).val();
  $('.field-{$form_name}-page').hide();
  $('.field-{$form_name}-module').hide();
  $('.field-{$form_name}-category').hide();
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
                ],

            ],
            'type' => [
                'type' => 'dropDownList',
                'data' => $this->data_types,
            ],
            'category' => [
                'relation' => [
                    'class' => Category::className(),
                    'query'=>[
                        'where'=>[
                            'parent_id'=>null
                        ]
                    ]
                ],
                'field_options' => [
                    'options' => ['style' => ($this->type == 'category') ? '' : 'display:none'],
                ]
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
			'img' => [
                'type' => 'img',
                'params' => [
                    'deleted' => true,
                ],
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
        $result = [
            'form_action' => [$controller_name . '/save'],
            'cancel' => ["$controller_name/index"],
            'fields' => $fields,
        ];
        return $result;
    }
    public function createUrl()
    {
        if ($this->type == 'category') {
            $result = Url::to(['site/catalog', 'id' => $this->owner_id]);
        } else {
            $result = parent::createUrl();
        }
        return $result;
    }
}
