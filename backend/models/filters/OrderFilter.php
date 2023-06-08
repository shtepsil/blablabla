<?php
namespace backend\models\filters;

use yii\base\Model;
use yii\db\ActiveRecord;

class OrderFilter extends Model
{
    /** @var int $number */
    public $number;

    /** @var int $status */
    public $status;

    /** @var int $manager */
    public $manager;

    /** @var int $sum_start */
    public $sum_start;

    /** @var int $sum_end */
    public $sum_end;

    /** @var int $order_create_start */
    public $order_create_start;

    /** @var int $order_create_end */
    public $order_create_end;

    /** @var string $buyer_name */
    public $buyer_name;

    /** @var string $buyer_email */
    public $buyer_email;

    /** @var string $buyer_phone */
    public $buyer_phone;

    /** @var string $good */
    public $good;

    /** @var int $order_delivery_start */
    public $order_delivery_start;

    /** @var int $order_delivery_end */
    public $order_delivery_end;

    /** @var int $payment_type */
    public $payment_type;

    /** @var int $payment_status */
    public $payment_status;

    /** @var int $delivery */
    public $delivery;

    /** @var int $pickpoint */
    public $pickpoint;

    /** @var int $town */
    public $town;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number', 'status', 'manager', 'sum_start', 'sum_end', 'order_create_start', 'order_create_end',
                'order_delivery_start', 'order_delivery_end', 'payment_type', 'delivery', 'pickpoint'], 'integer'],
            [['buyer_name', 'buyer_email', 'buyer_phone', 'good', 'payment_status'], 'string', 'max' => 255],
            [['number', 'status', 'manager', 'sum_start', 'sum_end', 'order_create_start', 'order_create_end',
                'order_delivery_start', 'order_delivery_end', 'payment_type', 'delivery', 'pickpoint', 'buyer_name', 'buyer_email', 'buyer_phone', 'good', 'payment_status'], 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            [['buyer_phone'], 'filter', 'filter' =>  function ($value) {
                $value = str_replace('_', '', $value);
                $value = str_replace('--', '', $value);
                $value = (
                substr($value, -1) == '-' || substr($value, -1) == ')' || substr($value, -1) == '('
                    ? substr($value, 0, -1) : $value);
                return $value;
            }],
            [['buyer_name'], 'match', 'pattern' => '/^[a-z\- а-яё]+$/ui', 'message' => 'Не допустимые символы'],
            [['buyer_email'], 'match', 'pattern' => '/^[a-z\-_а-яё@\.]+$/ui', 'message' => 'Не допустимые символы'],
            [['buyer_phone'], 'match', 'pattern' => '/^[0-9\-\(\)\+ ]+$/ui', 'message' => 'Не допустимые символы'],
            [['good'], 'match', 'pattern' => '/^[a-z\- _а-яё0-9\.,\%\/]+$/ui', 'message' => 'Не допустимые символы'],
        ];
    }

    public function beforeValidate()
    {
        if ($this->order_create_start) {
            $this->order_create_start = strtotime($this->order_create_start);
        }

        if ($this->order_create_end) {
            $this->order_create_end = strtotime($this->order_create_end);
        }

        if ($this->order_delivery_start) {
            $this->order_delivery_start = strtotime($this->order_delivery_start);
        }

        if ($this->order_delivery_end) {
            $this->order_delivery_end = strtotime($this->order_delivery_end);
        }

        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }
}