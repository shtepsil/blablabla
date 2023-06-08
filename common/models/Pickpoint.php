<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;
use shadow\helpers\StringHelper;

/**
 * This is the model class for table "city".
 *
 * @property integer $id
 * @property bool $active
 * @property string $name
 * @property string $desc
 * @property string $coordinate
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $city_id
 *
 * @property ItemsCount[] $itemsCounts
 */
class Pickpoint extends \shadow\SActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pickpoint';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
			[
                'class' => '\shadow\behaviors\SaveRelationBehavior',
                'relations' => [
                    PickpointImg::className() => [
                        'type' => 'img',
                        'attribute' => 'pickpoint_id',  
                        'extra_attributes' => [
                            'sort',
                            'name'
                        ]
                    ]
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'coordinate'], 'required'],
            [['name','coordinate', 'desc', 'time_work', 'phones', 'images'], 'string', 'max' => 255],
            ['active', 'default', 'value' => true],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
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
            'active' => 'Активность',
            'desc' => 'Описание',
            'coordinate' => 'Координаты',
            'city_id' => 'Город',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
			'time_work' => 'Время работы',
            'phones' => 'Телефоны'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        $city = City::findOne(['id' => $this->city_id]);

        return ($city ? $city->name : $this->city_id);
    }

    public function FormParams()
    {
        if ($this->isNewRecord) {
            $this->loadDefaultValues(true);
        }

        $controller_name = Inflector::camel2id(Yii::$app->controller->id);
		
		
		 $relation_imgs = PickpointImg::find()->where(array('pickpoint_id' =>  $this->id))->orderBy(['sort' => SORT_ASC])->all();
                foreach ($relation_imgs as $value) {
                    $imgs[] = [
                        'name' => StringHelper::basename($value->url),
                        'size' => 0,
                        'url' => $value->url,
                        'title' => $value->name,
                        'sort' => $value->sort,
                        'id' => $value->id
                    ];
                }
		
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
                        'active' => [
                            'type' => 'checkbox'
                        ],
                        'city_id' => [
                            'type' => 'dropDownList',
                            'data' => City::find()->select(['name', 'id'])->indexBy('id')->column(),
                            'params' => [
                                'multiple' => false,
                            ]
                        ],
                        'desc' => [],
                        'coordinate' => [],
						'time_work' => [],
                        'phones' => []
                    ]
                ],
                'imgs' => [
                    'title' => 'Изображения',
                    'icon' => 'picture-o',
                    'options' => [],
                    'fields' => [
                        'js_files' => [
                            'files' => [
                                'name' => 'pickpointImg',
                                'filters' => [
                                    'imageFilter' => true,
                                ],
                                'isSort' => true,
                                'isName' => true,
                                'value' => $imgs
                            ]
                        ],
                    ]
                ]
            ]
        ];
  
        return $result;
    }
}