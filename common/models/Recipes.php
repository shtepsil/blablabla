<?php

namespace common\models;

use shadow\assets\Select2Assets;
use shadow\helpers\StringHelper;
use shadow\widgets\CKEditor;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\helpers\Inflector;
use yii\web\UploadedFile;

use yii\helpers\Url;
use shadow\multilingual\behaviors\MultilingualBehavior;
use shadow\multilingual\behaviors\MultilingualQuery;
use shadow\plugins\seo\behaviors\SSeoBehavior;
use shadow\SResizeImg;

/**
 * This is the model class for table "recipes".
 *
 * @property integer $id
 * @property string $name
 * @property string $time_cooking
 * @property string $small_body
 * @property string $img_list
 * @property integer $isVisible
 * @property integer $isDay
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property RecipesImg[] $recipesImgs
 * @property RecipesItem[] $recipesItems
 * @property RecipesMethod[] $recipesMethods
 * @property Items[] items
 */
class Recipes extends \shadow\SActiveRecord
{
	use SResizeImg;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recipes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['isVisible', 'isDay', 'toMain'], 'integer'],
            [['name', 'time_cooking'], 'string', 'max' => 255],
            [['small_body'], 'string', 'max' => 500],
            [['description_time_cooking'], 'string', 'max' => 1000],
            ['isVisible', 'default', 'value' => true],
            [['isDay'], 'default', 'value' => 0],
            ['img_list', 'image', 'extensions' => 'jpg, gif, png, jpeg'],
            [['items'], 'safe']
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
            'time_cooking' => 'Время приготовления',
            'description_time_cooking' => 'Краткое описание приготовления',
            'small_body' => 'Краткое описание',
            'img_list' => 'Изображения для списковой',
            'isVisible' => 'Видимость',
            'isDay' => 'Рецепт дня',
            'toMain' => 'На главную',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipesImgs()
    {
        return $this->hasMany(RecipesImg::className(), ['id_recipes' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipesMethods()
    {
        return $this->hasMany(RecipesMethod::className(), ['recipe_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipesItems()
    {
        return $this->hasMany(RecipesItem::className(), ['recipe_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Items::className(), ['id' => 'item_id'])->via('recipesItems');
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

    /**
     * @return array
     */
    public function FormParams()
    {

//        d::pri($this->time_cooking);

        $controller_name = Inflector::camel2id(Yii::$app->controller->id);
        $imgs = [];
        if ($this->isNewRecord) {
            $this->loadDefaultValues(true);
        } else {
            /**@var $relation_imgs RecipesImg[] */
            $relation_imgs = RecipesImg::find()->where(['id_recipes' => $this->id])->all();
            foreach ($relation_imgs as $value) {
                $imgs[] = [
                    'name' => StringHelper::basename($value->url),
                    'size' => 0,
//                    'type' => mime_content_type(Yii::getAlias('@frontend'). $value->url),
                    'url' => $value->url,
                    'id' => $value->id
                ];
            }
        }

        /*
         * Подготавливаем переменные для шаблона поля "Время приготовления"
         */
        $arr_time = explode(':',$this->time_cooking);
        $recipe = [
            'hours' => $arr_time[0],
            'minutes' => $arr_time[1],
            'small_desc' => $this->description_time_cooking,
        ];

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
                        'isDay' => [
                            'type' => 'checkbox'
                        ],
                        'toMain' => [
                            'type' => 'checkbox'
                        ],
                        'name' => [
                            'title' => 'Название'
                        ],
                        'time_cooking' => [
                            'params'=>[
                                'class'=>'quantity-num',
                            ],
                            'type' => 'hidden',
//
                            'field_options'=>[
                                'inputTemplate'=>
                                    Yii::$app->view->renderFile(
                                        '@app/views/blocks/recipe/field-time-coolking.php',$recipe
                                    ),
                                'inputOptions' => [
                                    'placeholder' => 'Введите число времени',
                                    'value' => $this->time_cooking,
                                ]
                            ],
                            'labelOptions'=>[
                                'name'=>'Время приготовления',
                            ],
                        ],
                        'description_time_cooking' => [
                            'type' => 'textArea',
                            'field_options'=>[
                                'options'=>[
                                    'class'=>'dn',
                                ],
                            ],
                            'params'=>[
                                'class'=>'description-time-cooking',
                                'value' => $this->description_time_cooking,
                            ],
                        ],
                        'img_list' => [
                            'type' => 'img',
                        ],
                        'small_body' => [
                            'type' => 'textArea',
                        ],
                    ],
                ],
                'imgs' => [
                    'title' => 'Изображения',
                    'icon' => 'picture-o',
                    'options' => [],
                    'fields' => [
                        'js_files' => [
                            'files' => [
                                'name' => 'recipesImg',
                                'filters' => [
                                    'imageFilter' => true,
                                ],
                                'value' => $imgs
                            ]
                        ],
                    ]
                ],
                'recipe_method' => [
                    'title' => 'Способ приготовления',
                    'icon' => 'th-list',
                    'options' => [],
                    'render' => [
                        'view' => 'method'
                    ]
                ],
                'recipe_items' => [
                    'title' => 'Товары',
                    'icon' => 'th-list',
                    'options' => [],
                    'render' => [
                        'view' => 'items'
                    ]
                ],
            ]
        ];
        return $result;
    }

    public function AllItems()
    {
        $result = [];
        if ($this->isNewRecord) {
            return $result;
        }
        $q = new Query();
        $q->select([
            '`t`.name',
            '`t`.id',
            '`t`.count',
            '`t`.item_id',
            '`t`.item_count',
        ]);
        $q->andWhere(['`t`.recipe_id' => $this->id]);
        $rows = $q->from('recipes_item t')->all();
        foreach ($rows as $value) {
            if (!isset($result[$value['id']])) {
                $item_count = (double)$value['item_count'];
                $result[$value['id']] = [
                    'name' => $value['name'],
                    'id' => $value['id'],
                    'count' => $value['count'],
                    'item_id' => $value['item_id'],
                    'item_count' => $item_count ? $item_count : '',
                ];
            }
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
//            $q->andWhere(['`items`.`isDeleted`' => 0]);
        }
        return $q;
    }
    public function behaviors()
    {
        $result = [
            TimestampBehavior::className(),
            [
                'class' => '\shadow\behaviors\UploadFileBehavior',
                'attributes' => ['img_list'],
            ],
            [
                'class' => '\shadow\behaviors\SaveRelationBehavior',
                'relations' => [
                    RecipesImg::className() => [
                        'type' => 'img',
                        'attribute' => 'id_recipes'
                    ],
                ],
            ],
        ];
		if (SSeoBehavior::enableSeoEdit()) {
			$result['seo'] = [
				'class' => SSeoBehavior::className(),
				'nameTranslate' => 'name',
				'controller' => 'site',
				'action' => 'recipe',
			];
		}
		return $result;
    }
    
//    public $watermark_path = '/uploads/watemark_toolsmart-8.png';

    /**
     * @param bool $resize
     * @param string|array $size_type
     * @param bool $array
     * @return string|array
     */
    public function img($resize = false, $size_type = 'mini', $array = false)
    {
        if (!$array) {
            if ($this->img_list) {
                if ($resize && isset(ItemImg::$_size_img_a[$size_type])) {
                    $result = $this->resizeImg(ItemImg::$_size_img_a[$size_type], 'img_list');
                } else {
                    $result = $this->img_list;
                }
            } else {
                if (isset($this->recipesImgs[0]->url)) {
                    if ($resize) {
                        $result = $this->recipesImgs[0]->resizeImg($size_type);
                    } else {
                        $result = $this->recipesImgs[0]->url;
                    }
                } else {
                    $result = '/uploads/no_image.png';
                }
            }
            if (!$result) {
                $result = '/uploads/no_image.png';
            }
            if ($result != '/uploads/no_image.png' && !is_file(Yii::getAlias('@frontend/web') . $result)) {
                $result = '/uploads/no_image.png';
            }
        } else {
            $result = [];
            if ($this->recipesImgs) {
                $result = array();
                foreach ($this->recipesImgs as $img) {
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
                if ($this->img_list) {
                    if ($resize) {
                        if (is_array($size_type)) {
                            $img_size = [];
                            foreach ($size_type as $value) {
                                if (!isset(ItemImg::$_size_img_a[$value])) {
                                    if (is_file(Yii::getAlias('@frontend/web') . $this->img_list)) {
                                        $img_size[$value] = $this->img_list;
                                    }else{
                                        $img_size[$value] = null;
                                    }
                                    continue;
                                }
                                if ($img_resize = $this->resizeImg(ItemImg::$_size_img_a[$value], 'img_list')) {
                                    $img_size[$value] = $img_resize;
                                }
                            }
                            $img_size['title'] = $this->name;
                            $result[] = $img_size;
                        } else {
                            if (isset(ItemImg::$_size_img_a[$size_type]) && ($img_resize = $this->resizeImg(ItemImg::$_size_img_a[$size_type], 'img_list'))) {
                                $result[] = $img_resize;
                            }
                        }
                    } else {
                        if (is_file(Yii::getAlias('@frontend/web') . $this->img_list)) {
                            $result[] = $this->img_list;
                        }
                    }
                }
            }
            if (!$result) {
                $result[] = '/uploads/no_image.png';
            }
        }
        return $result;
    }

    /*
     * $size_type - массив/строка размеров,
     * которые должны быть у созданных копий
     */
    public function seoImg($size_type){
        $result = [];
        if ($this->recipesImgs) {

//            d::pri('$this->recipesImgs');

            foreach ($this->recipesImgs as $key=>$img) {
                if (is_array($size_type)) {
                    $img_size = [];
                    foreach ($size_type as $value) {
                        if ($img_resize = $img->resizeImg($value)) {
                            $img_size[$value] = $img_resize;
                        }else{
                            $img_size[$value] = '';
                        }
                    }
                    $result[] = $img_size;
                } else {
                    if ($img_resize = $img->resizeImg($size_type)) {
                        $result[] = $img_resize;
                    }
                }
            }
        }elseif ($this->img_list) {

//            d::pri('$this->img_list');

            if (is_array($size_type)) {
                $img_size = [];
                foreach ($size_type as $value) {
                    if (!isset(ItemImg::$_size_img_a[$value])) {
                        if (is_file(Yii::getAlias('@frontend/web') . $this->img_list)) {
                            $img_size[$value] = $this->img_list;
                        }else{
                            $img_size[$value] = null;
                        }
                        continue;
                    }
                    if ($img_resize = $this->resizeImg(ItemImg::$_size_img_a[$value], 'img_list')) {
                        $img_size[$value] = $img_resize;
                    }
                }
                $result[] = $img_size;
            } else {
                if (isset(ItemImg::$_size_img_a[$size_type]) && ($img_resize = $this->resizeImg(ItemImg::$_size_img_a[$size_type], 'img_list'))) {
                    $result[] = $img_resize;
                }
            }
        }
        return $result;
    }

    /**
     * This method is called at the end of inserting or updating a record.
     * The default implementation will trigger an [[EVENT_AFTER_INSERT]] event when `$insert` is true,
     * or an [[EVENT_AFTER_UPDATE]] event if `$insert` is false. The event class used is [[AfterSaveEvent]].
     * When overriding this method, make sure you call the parent implementation so that
     * the event is triggered.
     * @param boolean $insert whether this method called while inserting a record.
     * If false, it means the method is called while updating a record.
     * @param array $changedAttributes The old values of attributes that had changed and were saved.
     * You can use this parameter to take action based on the changes made for example send an email
     * when the password had changed or implement audit trail that tracks all the changes.
     * `$changedAttributes` gives you the old attribute values while the active record (`$this`) has
     * already the new, updated values.
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->saveRecipesItems();
        $this->saveRecipesMethod($insert);
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }
    public function saveRecipesItems()
    {
        $data = Yii::$app->request->post('recipesItems', []);
        $items = $this->AllItems();
        $model = new RecipesItem();
        if ($data) {
            foreach ($data as $key => $value) {
                if (!isset($items[$key])) {
                    if ($value['name']) {
                        $record = new RecipesItem();
                        $record->setAttributes($value);
                        $record->recipe_id = $this->id;
                        $record->save(false);
                    }
                } else {
                    Yii::$app->db->createCommand()->update($model->tableName(), $value, ['id' => $key])->execute();
                    unset($items[$key]);
                }
            }
        }
        if ($items) {
            $deleted = [];
            foreach ($items as $item) {
                $deleted[] = $item['id'];
            }
            if ($deleted) {
                Yii::$app->db->createCommand()->delete($model->tableName(), array('id' => $deleted))->execute();
            }
        }
    }
    public function saveRecipesMethod($insert)
    {
        $data = Yii::$app->request->post('recipesMethod', []);
        if ($insert) {
            $items = [];
        } else {
            $items = RecipesMethod::find()->indexBy('id')->andWhere(['recipe_id'=>$this->id])->all();
        }
        $model = new RecipesMethod();
        if ($data) {
            foreach ($data as $key => $value) {
                if (!isset($items[$key])) {
                    if ($value['body']) {
                        $record = new RecipesMethod();
                        $record->setAttributes($value);
                        $record->recipe_id = $this->id;
                        $record->img = UploadedFile::getInstanceByName("recipesMethod[$key][img]");
                        $record->save(false);
                    }
                } else {
                    /**@var $target_item RecipesMethod*/
                    $target_item = $items[$key];
                    $target_item->setAttributes($value);
                    $target_item->recipe_id = $this->id;
                    $target_item->img = UploadedFile::getInstanceByName("recipesMethod[$key][img]");
                    $target_item->save(false);
                    unset($items[$key]);
                }
            }
        }
        if ($items) {
            $deleted = [];
            foreach ($items as $item) {
                $deleted[] = $item->id;
            }
            if ($deleted) {
                Yii::$app->db->createCommand()->delete($model->tableName(), array('id' => $deleted))->execute();
            }
        }
    }
	
    public function url()
    {
        $params[0] = 'site/recipe';
        $params['id'] = $this->id;
        return Url::to($params);
    }
	
}//Class
