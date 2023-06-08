<?php
namespace common\models;

use Yii;

/**
 * This is the model class for table "items_together".
 *
 * @property integer $id
 * @property integer $item_main_id
 * @property integer $item_id
 * @property string $discount
 * @property string $count
 *
 * @property Items $itemMain
 * @property Items $item
 */
class ItemsTogether extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'items_together';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_main_id', 'item_id'], 'required'],
            [['item_main_id', 'item_id'], 'integer'],
            [['count'], 'number'],
            [['discount'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_main_id' => 'К которому привязали',
            'item_id' => 'Привязаный товар',
            'discount' => 'Скидка/Цена',
            'count' => 'Count',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemMain()
    {
        return $this->hasOne(Items::className(), ['id' => 'item_main_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Items::className(), ['id' => 'item_id']);
    }
    public function real_price($count = 1)
    {
        $discount = preg_replace("#([^\d%]*)#u", '', $this->discount);
        if ($discount) {
            if (preg_match("#\%$#u", $discount)) {
                $discount = preg_replace("#\%$#u", '', $discount);
                $price = $this->item->sum_price($count);
                $price = round(((double)$price * (100 - (double)$discount)) / 100);
            } else {
                $price = $this->item->sum_price($count,'main',$this->discount);
            }
        }else{
            $price = $this->item->sum_price($count);
        }
        return $price;
    }
}
