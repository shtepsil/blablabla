<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * This is the model class for table "reviews_item".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $item_id
 * @property integer $rate
 * @property string $name
 * @property string $body
 * @property integer $isVisible
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Items $item
 * @property User $user
 */
class ReviewsItem extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reviews_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'name', 'body'], 'required'],
            [['rate'], 'integer', 'min' => 1, 'max' => 5],
            [['item_id', 'isVisible','user_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['body'], 'string', 'max' => 1000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id' => 'Товар',
            'user_id' => 'Пользователь',
            'rate' => 'Оценка',
            'name' => 'Имя',
            'body' => 'Отзыв',
            'isVisible' => 'Видимость',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Items::className(), ['id' => 'item_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    public function FormParams()
    {
        if ($this->isNewRecord) {
            $this->loadDefaultValues(true);
        }
        $controller_name = Inflector::camel2id(Yii::$app->controller->id);

        $result = [
            'form_action' => ["$controller_name/save"],
            'cancel' => ["site/$controller_name"],
            'fields' => [
                'isVisible' => [
                    'type' => 'checkbox'
                ],
                'user_id'=>[
                    'type' => 'dropDownList',
                    'data' => ArrayHelper::merge(
                        [''=>"Нет"],
                        User::find()->select(['username', 'id'])->indexBy('id')->column()
                    ),
                ],
                'item_id'=>[
                    'relation'=>[
                        'class' => 'common\models\Items',
                    ]
                ],
                'rate' => [],
                'name' => [],
                'body' => [
                    'type'=>'textArea'
                ],
            ],
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
        $sql=new Expression(
            '(select avg(rate) from `reviews_item` WHERE `reviews_item`.`item_id`=:item_id AND `reviews_item`.isVisible=1 group by `reviews_item`.`item_id`)',
            [':item_id'=>$this->item_id]
        );
        Items::updateAll(['popularity' => $sql], ['id' => $this->item_id]);
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

}
