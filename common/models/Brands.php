<?php

namespace common\models;

use shadow\SActiveRecord;
use Yii;
use yii\helpers\Url;
use shadow\widgets\CKEditor;
use shadow\plugins\seo\behaviors\SSeoBehavior;
  
/**
 * This is the model class for table "brands".
 *
 * @property integer $id
 * @property string $name
 * @property string $country
 * @property integer $isVisible
 * @property integer $isBanner
 * @property string $body
 * @property integer $isBanner
 *
 * @property Items[] $items
 */
class Brands extends SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'brands';
    }
 
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'trim'],
            ['name', 'unique','targetAttribute'=>'name'],
            [['name'], 'required'],
            [['name'], 'unique'],
            [['isVisible'], 'integer'],
			[['isBanner'], 'integer'],
			[['body'], 'string'],
			[
				'img', 'image',
				'extensions' => 'jpg, gif, png, jpeg'
			],
            [['name', 'country'], 'string', 'max' => 255]
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
            'country' => 'Страна',
            'isVisible' => 'Видимость',
			'isBanner' => 'В карусель на главную',
			'img' => 'Изображение',
			'body' => 'Текст',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Items::className(), ['brand_id' => 'id']);
    }
    public function FormParams()
    {
        if ($this->isNewRecord) {
            $this->isVisible = 1;
        }
        $result = [
            'form_action' => ['brands/save'],
            'cancel' => ['site/brands'],
            'fields' => [
                'isVisible' => [
                    'type' => 'checkbox'
                ],
				'isBanner' => [
                    'type' => 'checkbox'
                ],
                'name' => [
                    'title' => 'Название'
                ],
                'country' => [],
				'img' => [
					'type' => 'img'
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
        ];
        return $result;
    }
	public function url($params = [])
	{
		$params[0] = 'brands/';
		$params['id'] = $this->id;
		return Url::to($params);
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
                'action' => 'brands',
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
        }
        return $q;
    }
	
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
	
	
	
	
}
