<?php


namespace apiking\models\resources;


use common\models\Orders;


class OrderResource extends Orders
{
    private static $fieldsToRemove = [
        'city_id',
        'isEntity',
        'code',
        'full_purch_price',
        'bonus_use',
        'bonus_add',
        'pay_status',
        'manager_id',
        'driver_id',
        'collector_id',
        'updated_at',
        'isFast',
        'id_1c',
        'enable_bonus',
        'promo_code_id',
        'delivery_method',
        'invoice_file',
        'isApp',
        'hand_link',
        'isDeadline',
        'version_edit',
        'claim_id',
        'coordinates_json_yandex',
        'pickpoint_id',
        'isPhoneOrder',
        'bonus_manager',
        'bonus_driver',
    ];

    public function fields()
    {
        $fields = parent::fields();

        foreach (self::$fieldsToRemove as $fieldToRemove) {
            if (array_key_exists($fieldToRemove, $fields)) {
                unset($fields[$fieldToRemove]);
            }
        }

        return array_merge($fields, ['ordersItems']);
    }

    public function afterFind()
    {
        //$this->status = $this->data_status[$this->status];
        parent::afterFind();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersItems()
    {
        return $this->hasMany(OrderItemResource::className(), ['order_id' => 'id']);
    }
}