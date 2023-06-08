<?php

namespace common\models;

use shadow\assets\Select2Assets;
use shadow\plugins\datetimepicker\DateTimePicker;
use shadow\widgets\CKEditor;
use Yii;
use yii\helpers\Inflector;

/**
 * This is the model class for table "spec_actions".
 *
 * @property integer $id
 * @property string $name
 * @property integer $date_start
 * @property integer $date_end
 *
 *
 */
class SpecActions extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'spec_actions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['date_start'], 'date', 'timestampAttribute' => 'date_start', 'format' => 'php:d/m/Y'],
            [['date_end'], 'date', 'timestampAttribute' => 'date_end', 'format' => 'php:d/m/Y'],
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
            'date_start' => 'Дата начала',
            'date_end' => 'Дата окончания',
        ];
    }

//    /**
//     * @return \yii\db\ActiveQuery
//     */
//    public function getItemsCounts()
//    {
//        return $this->hasMany(ItemsCount::className(), ['city_id' => 'id']);
//    }
    public function FormParams()
    {
        if ($this->isNewRecord) {
            $this->loadDefaultValues(true);
        } else {
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
                        'name' => [],
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
                    ],
                ],
            ]
        ];
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
}
