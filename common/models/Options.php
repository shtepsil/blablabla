<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "options".
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 *
 * @property ItemOptionsValue[] $itemOptionsValues
 * @property OptionsCategory[] $optionsCategories
 * @property OptionsValue[] $optionsValues
 */
class Options extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'options';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 50]
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemOptionsValues()
    {
        return $this->hasMany(ItemOptionsValue::className(), ['option_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptionsCategories()
    {
        return $this->hasMany(OptionsCategory::className(), ['option_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptionsValues()
    {
        return $this->hasMany(OptionsValue::className(), ['option_id' => 'id']);
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
        $this->type = 'multi_select';
        return parent::beforeValidate();
    }

    protected $types = [
        'multi_select' => 'Несколько значений'
    ];
    public function FormParams()
    {

        $result = [
            'form_action' => ['options/save'],
            'cancel' => ['options/index'],
            'groups' => [
                'main' => [
                    'title' => 'Основное',
                    'icon' => 'suitcase',
                    'options' => [],
                    'fields' => [
                        'name' => [
                            'title' => 'Название'
                        ],
                    ],
                ],
                'values' => [
                    'title' => 'Значения',
                    'icon' => 'th-list',
                    'options' => [],
                    'relation'=>[
                        'class'=>OptionsValue::className(),
                        'field'=>'option_id',
                        'attributes'=>[
                            'value'
                        ]
                    ]
                ]
            ]
        ];
        return $result;
    }
    public function behaviors()
    {
        return [
            [
                'class' => '\shadow\behaviors\SaveRelationBehavior',
                'relations' => [
                    OptionsValue::className()=>[
                        'attribute' => 'option_id',
                        'attribute_main'=>'value',
                        'attributes'=>['value']
                    ]
                ],
            ],
        ];
    }
}
