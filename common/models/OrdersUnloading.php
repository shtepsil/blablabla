<?php

namespace common\models;

use backend\components\ProcessOrder\Appointments\AppointmentAccess;
use backend\components\ProcessOrder\Status\StatusAccess;
use backend\models\SUser;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * This is the model class for table "orders_unloading".
 *
 * @property integer          $id
 * @property integer          $order_id
 *
 * @property OrdersItems[]    $ordersItems
 * @property OrdersSets[]     $ordersSets
 * @property OrdersComments[] $ordersComments
 * @property OrdersHistory[]  $ordersHistories
 */
class OrdersUnloading extends \shadow\SActiveRecord
{
    use AppointmentAccess;
    use StatusAccess;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders_unloading';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'order_id'        => 'ID сделки',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersItems()
    {
        return $this->hasMany(OrdersItems::className(), ['order_id' => 'order_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['id' => 'order_id']);
    }
	
}