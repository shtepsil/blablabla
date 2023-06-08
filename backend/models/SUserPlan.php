<?php

namespace backend\models;

use shadow\plugins\datetimepicker\DateTimePicker;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * This is the model class for table "s_user_plan".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $sum
 * @property integer $date_start
 * @property integer $date_end
 *
 * @property SUser $user
 */
class SUserPlan extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 's_user_plan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'sum'], 'integer'],
            [['sum', 'date_start', 'date_end'], 'required'],
            [['date_start', 'date_end'], 'match', 'pattern' => "/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/i"],
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
            'user_id' => 'Менеджер',
            'sum' => 'Сумма плана',
            'date_start' => 'Дата начала',
            'date_end' => 'Дата окончания',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(SUser::className(), ['id' => 'user_id']);
    }
    public function FormParams()
    {
        if ($this->isNewRecord) {
            $this->loadDefaultValues(true);
            $this->date_start = date('d/m/Y');
            $this->date_end = date('d/m/Y', strtotime('+1 month'));
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
                        'user_id' => [
                            'type' => 'dropDownList',
                            'data' => ArrayHelper::merge(
                                [''=>'Для Всех'],
                                SUser::find()->where(['role' => 'manager'])->select(['username', 'id'])->indexBy('id')->column()
                            ),
                        ],
                        'sum' => [],
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
                                        $('#suserplan-date_end').datetimepicker('setStartDate', e.date);
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
                ]
            ]
        ];
        return $result;
    }
}
