<?php

namespace common\models;

use shadow\widgets\CKEditor;
use yii;
use yii\helpers\Inflector;

/**
 * This is the model class for table "about_history".
 *
 * @property integer $id
 * @property string $year
 * @property string $body
 * @property integer $sort
 */
class AboutHistory extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'about_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['year', 'body'], 'required'],
            [['body'], 'string'],
            [['sort'], 'integer'],
            [['sort'],'default','value'=>0],
            [['year'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'year' => 'Год',
            'body' => 'Текст',
            'sort' => 'Порядок',
        ];
    }
    public function FormParams()
    {
        if ($this->isNewRecord) {
            $this->loadDefaultValues(true);
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
                        'year' => [],
                        'sort' => [],
                        'body' => [
                            'type' => 'textArea',
                            'widget' => [
                                'class' => CKEditor::className(),
                                'config' => [
                                    'editorOptions' => [
                                        'enterMode' => 2
                                    ]
                                ]
                            ]
                        ],
                    ],
                ],
            ]
        ];

        return $result;
    }
}
