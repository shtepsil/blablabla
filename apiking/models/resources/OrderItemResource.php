<?php


namespace apiking\models\resources;


use common\models\OrdersItems;
use common\models\OrdersItemsHanding;

class OrderItemResource extends OrdersItems
{
    public $processingMethods = [];

    private static $fieldsToRemoveFromItem = [
        'count',
        'status',
        'measure_price',
        'weight',
        'isVisible',
        'video',
        'img_list',
        'isHit',
        'isNew',
        'popularity',
        'purch_price',
        'cid',
        'brand_id',
        'body',
        'body_small',
        'feature',
        'storage',
        'delivery',
        'bonus_manager'
    ];

    private static $fieldsToRemove = [
        'order_id',
        'item_id',
        'count',
        'weight',
        'bonus_manager'
    ];

    public function rules()
    {
        return array_merge(parent::rules(), [['processingMethods'], 'safe']);
    }

    private function convertDataTo1cItem()
    {
        if (!$this->data) {
            return [];
        }

        try {
            $data = json_decode($this->data, JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return [];
        }

        foreach (self::$fieldsToRemoveFromItem as $field) {
            if (array_key_exists($field, $data)) {
                unset($data[$field]);
            }
        }

        return $data;
    }

    private function convertProcessingMethods()
    {
        $result = null;

        if ($this->ordersItemsHandings) {
            foreach ($this->ordersItemsHandings as $element) {
                /**
                 * @var OrdersItemsHanding $element
                */
                $result[] = [
                    'id' => $element->typeHandling->id,
                    'name' => $element->typeHandling->name
                ];
            }
        }

        return $result;
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->data = $this->convertDataTo1cItem();
        $this->processingMethods = $this->convertProcessingMethods();
    }

    public function fields()
    {
        $fields = parent::fields();

        foreach (self::$fieldsToRemove as $fieldToRemove) {
            if (isset($fields[$fieldToRemove])) {
                unset($fields[$fieldToRemove]);
            }
        }

        return array_merge($fields, ['processingMethods']);

    }
}