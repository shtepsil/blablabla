<?php
namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use shadow\widgets\CKEditor;
//use yii\behaviors\SluggableBehavior

use shadow\multilingual\behaviors\MultilingualBehavior;
use shadow\multilingual\behaviors\MultilingualQuery;
use shadow\plugins\seo\behaviors\SSeoBehavior;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property string $name
 * @property integer $isVisible
 * @property integer $isHideincatalog
 * @property integer $isDeleted
 * @property integer $parent_id
 * @property integer $sort
 * @property string $type
 *
 * @property Category $parent
 * @property Category[] $categories
 * @property Items[] $items
 * @property OptionsCategory[] $optionsCategories
 */
class Category extends \shadow\SActiveRecord
{

    public $data_types = array(
        'items' => 'Товары',
        'cats' => 'Категории'
    );
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
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
        if ($this->parent_id) {
            if ($this->parent_id!=$this->id) {
                $parent = Category::findOne($this->parent_id);
                if ($parent->type == 'cats') {
                    $count_parents = count($parent->allParents());
                    //$count_parents + 2 = добавляемый уровень категории
                    // +1 уровень который мы щас добавилм
                    // +1 уровень потому что мы считаем от родителя, а не от этого элемента
                    // и того +2
                    if ($count_parents + 2 > $this->parentsJoinLevels) {
                        $this->addError('parent_id', ($this->parentsJoinLevels+1).' уровня категории не может быть');
                    }
                } else {
                    $this->addError('parent_id', 'В этой категории находятся товары');
                }
            } else {
                $this->addError('parent_id', 'Категория не может быть вложена в саму себя');
            }
        } else {
            $this->parent_id = null;
        }
        if (!$this->isNewRecord) {
            if ($this->oldAttributes['type'] != $this->type) {
                $this->addError('type', 'Не возможно сменить вложение');
            }
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','title','body'], 'required'],
            [['name', 'title'], 'trim'],
            ['isVisible', 'default', 'value' => true],
			['isHideincatalog', 'default', 'value' => false],			
            ['parent_id', 'default', 'value' => NULL],
            ['type', 'default', 'value' => 'cats'],
            [['isVisible', 'isDeleted', 'parent_id', 'sort', 'isHideincatalog', 'isWholesale'], 'integer'],
            [['name', 'title'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 20],
			[['img'], 'image', 'extensions' => ['jpg', 'gif', 'png', 'jpeg']],
            [['slug'], 'string']
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
            'title' => 'Заголовок',
            'body' => 'Текст',
            'isVisible' => 'Видимость',
			'isHideincatalog' => 'Спрятать в каталоге',
            'isDeleted' => 'Удалена',
            'parent_id' => 'Родитель',
            'sort' => 'Порядок',
            'type' => 'Вложение',
			'img' => 'Картинка',
			'slug' => 'slug',
            'isWholesale' => 'Видно только оптовикам'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Items::className(), ['cid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptionsCategories()
    {
        return $this->hasMany(OptionsCategory::className(), ['cid' => 'id']);
    }
	/**
     * @inheritdoc
     */
    public static function find()
    {
        if (Yii::$app->function_system->enable_multi_lang()) {
            $q = new MultilingualQuery(get_called_class());
            if (Yii::$app->id == 'app-backend') {
                $q->multilingual();
            } else {
                $q->localized();
            }
        } else {
            $q = parent::find();
        }
        if (SSeoBehavior::enableSeoEdit()) {
            SSeoBehavior::modificationSeoQuery($q);
        }

        return $q;
    }
    public function behaviors(){
        $result = [
			/*[
            'class' => SluggableBehavior::className(),
            'attribute' => 'name',
            'slugAttribute' => 'slug',
        ],*/
//            [
//                'class' => '\shadow\behaviors\SaveRelationBehavior',
//                'relations' => [
//                    OptionsCategory::className()=>[
//                        'attribute' => 'cid',
//                        'attribute_main'=>'option_id',
//                        'attributes'=>['option_id']
//                    ]
//                ],
//            ],
		   [
                'class' => \shadow\behaviors\UploadFileBehavior::className(),
                'attributes' => [
                    'img_list',
					'img'
                ],
            ] 
        ];
		
        if (SSeoBehavior::enableSeoEdit()) {
            $result['seo'] = [
                'class' => SSeoBehavior::className(),
                'nameTranslate' => 'name',
                'controller' => 'site',
                'action' => 'catalog',
                'parentRelation' => 'parent',
                'childrenRelation' => [
                    'categories',
                    'items',
                ],
            ];
        }

        return $result;
    }
    public function FormParams()
    {
        $cats = Category::find()->where('parent_id is NULL')->all();
        $selects = (new Category())->SelectViewCat($cats, 0, [], ['items' => ['disabled' => true]]);
        $selects['data'] = ArrayHelper::merge([null => 'Нет'], isset($selects['data']) ? $selects['data'] : []);

        if ($this->isNewRecord) {
            $this->loadDefaultValues();
            $this->parent_id = Yii::$app->request->get('parent');
            if($this->parent_id){
                $this->sort = Category::find()->where(['parent_id'=>$this->parent_id])->count();
            }
        }else{
            $selects['options'][$this->id]['disabled'] = true;
        }
        $result = [
            'form_action' => ['category/save'],
            'cancel' => ['category/index'],
            'fields' => [
                'isVisible' => [
                    'type' => 'checkbox'
                ],
                'isWholesale' => [
                    'type' => 'checkbox'
                ],
                'name' => [
                    'title' => 'Название'
                ],
                'title' => [],
                'body' => [
                    'type' => 'textArea',
                    'widget' => [
                        'class' => CKEditor::className(),
                        'config' => [
                            'editorOptions' => [
                                'enterMode' => 0
                            ]
                        ]
                    ]
                ],
				'img' => [
					'type' => 'img',
					'params' => [
						'deleted' => true,
					],
				],
				'isHideincatalog' => [
                    'type' => 'checkbox'
                ],					
            ],
            'groups' => [
                'main' => [
                    'title' => 'Основное',
                    'icon' => 'suitcase',
                    'options' => [],
                    'fields' => [
                        'sort' => [],
                        'parent_id' => [
                            'type' => 'dropDownList',
                            'data' => isset($selects['data']) ? $selects['data'] : [],
                            'params' => [
                                'options' => isset($selects['options']) ? $selects['options'] : [],
                            ]
                        ],
                    ],
                ],
//                'values' => [
//                    'title' => 'Фильтры',
//                    'icon' => 'th-list',
//                    'options' => [],
//                    'relation'=>[
//                        'class'=>OptionsCategory::className(),
//                        'field'=>'cid',
//                        'attributes'=>[
//                            'option_id'=>[
//                                'type'=>'dropDownList',
//                                'relation'=>[
//                                    'class'=>Options::className()
//                                ]
//                            ]
//                        ]
//                    ]
//                ]
            ]
        ];
        if ($this->isNewRecord) {
            $result['fields']['type'] = [
                'type' => 'dropDownList',
                'data' => $this->data_types
            ];
        }
        return $result;
    }
    public $parentsJoinLevels = 3;
    public $parentAttribute = 'parent_id';
    public $sortAttribute = 'sort';
    public function allParents($depth = null)
    {
        $parentId = $this->parent_id;
        $result = [];
        if ($this->parent_id) {
            $result[] = $this->parent_id;
        }
        $tableName = $this->tableName();
        $primaryKey = $this->primaryKey();
        if (!isset($primaryKey[0])) {
            throw new Exception('"' . $this->className() . '" must have a primary key.');
        }
        $primaryKey = $primaryKey[0];
        $depthCur = 1;
        while ($parentId !== null && ($depth === null || $depthCur < $depth)) {
            $query = (new Query())
                ->select(["lvl0.[[{$this->parentAttribute}]] AS lvl0"])
                ->from("{$tableName} lvl0")
                ->where(["lvl0.[[{$primaryKey}]]" => $parentId]);
            for ($i = 0; $i < $this->parentsJoinLevels && ($depth === null || $i + $depthCur + 1 < $depth); $i++) {
                $j = $i + 1;
                $query
                    ->addSelect(["lvl{$j}.[[{$this->parentAttribute}]] as lvl{$j}"])
                    ->leftJoin("{$tableName} lvl{$j}", "lvl{$j}.[[{$primaryKey}]] = lvl{$i}.[[{$this->parentAttribute}]]");
            }
            foreach ($query->one($this->getDb()) as $parentId) {
                $depthCur++;
                if ($parentId === null) {
                    break;
                }
                $result[] = $parentId;
            }
        }
        return $result;
    }
    public function parentID()
    {
        if($this->parent_id){
            $id = $this->parent_id;
        }else{
            $id = $this->id;
        }
        return $id;
    }
    public function getSubCats()
    {
        return $this->getCategories()->where(['isVisible'=>1])->all();
    }
    /**
     * @param bool $array_id Отдавать только id
     * @param bool $all Отдавать все категории
     * @return array $result
     */
    public function getAllSubItemCats($array_id=true,$all=false)
    {
        /**
         * @var Category $cat
         */
        $result = [];

        foreach ($this->getSubCats() as $cat) {
            if ($cat->type=='items') {
                if ($array_id) {
                    $result[] = $cat->id;
                }else{
                    $result[] = $cat;
                }
            } else {
                if($all){
                    if ($array_id) {
                        $result[] = $cat->id;
                    }else{
                        $result[] = $cat;
                    }
                }
                $result = ArrayHelper::merge($result, $cat->getAllSubItemCats($array_id,$all));
            }
        }
        return $result;
    }
	
	public function countItem($condition = ['isVisible' => 1])
    {
        /**
         * @var Category[] $cats
         */
        if ($this->type == 'items') {
            $id_cats = $this->id;
        } else {
            $id_cats = $this->getAllSubItemCats();
        }
        $q = Items::find();
        $q->andWhere($condition);
        $q->andWhere(['cid' => $id_cats]);
        $result = $q->count();

        return (int)$result;
    }
	
    public function array_lists()
    {
        $result = [];
        $parents = Category::findAll(array('parent_id' => null));
        foreach ($parents as $parent) {
            $result[$parent->getPrimaryKey()]['main'] = $parent;
            $result[$parent->getPrimaryKey()]['children'] = $parent->getChildren();
        }
        return $result;
    }
    public function getChildren()
    {
        /**
         * @var Category[] $children
         */
        $result = [];
        $children = $this->hasMany($this->className(), [$this->parentAttribute => 'id'])
            ->orderBy([$this->sortAttribute => SORT_ASC])->all($this->getDb());
        foreach ($children as $value) {
            $result[$value->getPrimaryKey()]['main'] = $value;
            $result[$value->getPrimaryKey()]['children'] = $value->getChildren();
        }
        return $result;
    }
    public function SelectViewCat($cats, $count = 0, $data = [], $options = [])
    {
        if ($cats instanceof Category == false) {
            foreach ($cats as $cat) {
                if ($cat->type == 'items') {
                    if (isset($options['items'])) {
                        $data['options'][$cat->id] = $options['items'];
                    }
                }
                if ($cat->type == 'cats') {
                    if (isset($options['cats'])) {
                        $data['options'][$cat->id] = $options['cats'];
                    }
                }
                if ($cat->categories) {
                    $data['data'][$cat->id] = str_repeat('-', $count) . $cat->name;
                    $data = $this->SelectViewCat($cat->categories, $count + 1, $data, $options);
                } else {
                    $data['data'][$cat->id] = str_repeat('-', $count) . $cat->name;
                }
            }
        }
        if ($cats instanceof Category) {
            if ($cats->type == 'items') {
                if (isset($options['items'])) {
                    $data['options'][$cats->id] = $options['items'];
                }
            }
            if ($cats->type == 'cats') {
                if (isset($options['cats'])) {
                    $data['options'][$cats->id] = $options['cats'];
                }
            }
            $data['data'][$cats->id] = str_repeat('-', $count) . $cats->name;
        }
        return $data;
    }
    public function url($params=[])
    {
        $params[0] = 'site/catalog';
        $params['id'] = $this->id;
//		$params['slug'] = $this->slug;
        return Url::to($params);
    }
}
