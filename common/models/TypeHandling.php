<?php

namespace common\models;

use Yii;
use yii\helpers\Inflector;

/**
 * This is the model class for table "type_handling".
 *
 * @property integer $id
 * @property string $name
 * @property string $img
 * @property integer $isVisible
 *
 * @property ItemsTypeHandling[] $itemsTypeHandlings
 * @property OrdersItemsHanding[] $ordersItemsHandings
 */
class TypeHandling extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'type_handling';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['isVisible'], 'integer'],
            [['name'], 'string', 'max' => 255],
            ['img', 'image', 'extensions' => 'jpg, gif, png, jpeg'],
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
            'isVisible' => 'Видимость',
            'img' => 'Изображение',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemsTypeHandlings()
    {
        return $this->hasMany(ItemsTypeHandling::className(), ['type_handling_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersItemsHandings()
    {
        return $this->hasMany(OrdersItemsHanding::className(), ['type_handling_id' => 'id']);
    }
    public function behaviors()
    {
        return [
            [
                'class' => '\shadow\behaviors\UploadFileBehavior',
                'attributes' => ['img'],
            ],
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
            'fields' => [
                'isVisible' => [
                    'type' => 'checkbox'
                ],
                'name' => [],
                'img' => [
                    'type' => 'img'
                ],
            ],
        ];
        return $result;
    }
}
