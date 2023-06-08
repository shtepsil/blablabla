<?php

namespace common\models;

use shadow\widgets\CKEditor;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "jobs".
 *
 * @property integer $id
 * @property string $name
 * @property string $body
 * @property integer $isVisible
 * @property integer $created_at
 * @property integer $updated_at
 */
class Jobs extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jobs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'body' ], 'required'],
            [['isVisible'], 'integer'],
            [['name', 'body'], 'string', 'max' => 255]
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
            'body' => 'Текст',
            'isVisible' => 'Видимость',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function FormParams()
    {
        if ($this->isNewRecord) {
            $this->loadDefaultValues(true);
        }
        $result = [
            'form_action' => ['jobs/save'],
            'cancel' => ['site/jobs'],
            'groups' => [
                'main' => [
                    'title' => 'Основное',
                    'icon' => 'suitcase',
                    'options' => [],
                    'fields' => [
                        'isVisible' => [
                            'type' => 'checkbox'
                        ],
                        'name' => [],
                        'body' => [
                            'type' => 'textArea',
                            'widget' => [
                                'class' => CKEditor::className()
                            ]
                        ],
                    ],
                ]
            ]
        ];
        return $result;
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
