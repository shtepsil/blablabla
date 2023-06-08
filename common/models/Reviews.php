<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "reviews".
 *
 * @property integer $id
 * @property integer $rate
 * @property string $name
 * @property string $plus_body
 * @property string $minus_body
 * @property string $body
 * @property integer $isVisible
 * @property integer $created_at
 * @property integer $updated_at
 */
class Reviews extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reviews';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['isVisible',], 'integer'],
            [['rate'], 'integer', 'min' => 1, 'max' => 5],
            [['name', 'body'], 'required'],
            [['isVisible'], 'default', 'value' => 0],
            [['plus_body', 'minus_body', 'body'], 'string'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rate' => 'Оценка',
            'name' => 'Имя',
            'plus_body' => 'Достоинства',
            'minus_body' => 'Недостатки',
            'body' => 'Комментарий',
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
            'form_action' => ['reviews-site/save'],
            'cancel' => ['site/reviews-site'],
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
                        'rate' => [],
                        'plus_body' => [
                            'type' => 'textArea'
                        ],
                        'minus_body' => [
                            'type' => 'textArea'
                        ],
                        'body' => [
                            'type' => 'textArea'
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
