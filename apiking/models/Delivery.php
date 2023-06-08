<?php

namespace apiking\models;

use common\models\City;
use yii\base\Model;
use Yii;


class Delivery extends Model
{
    /**
     * @param float $sum
     * @param float $weight
     * @param int $cityId
     * @return false|float
     */
    public function getDelivery($sum, $weight, $cityId)
    {
        $delivery = -1;

        if ((int)$cityId > 0) {
            $city = City::find()->where(['id' => (int)$cityId])->limit(1)->one();

            if (!empty($city['isOnlyPickup']) && $city['isOnlyPickup'] == 1) {
                $delivery = -1;
            }
            elseif (!empty($city['delivery_free_sum']) && $sum > $city['delivery_free_sum']) {
                $delivery = 0;
            } elseif (!empty($city['price_delivery'])) {
                $delivery = $city['price_delivery'];
            } elseif (!empty($city['delivery_weight_sum']) && $weight > 0) {
                $delivery = $city['delivery_weight_sum'] * $weight;
            } else {
                $delivery = Yii::$app->function_system->deliveryPrice($sum, $city['id']);
            }
        }

        return round($delivery);
    }

    /**
     * @param int $cityId
     * @return false|float
     */
    public function getCostFreeDelivery($cityId)
    {
        $delivery = -1;

        if ((int)$cityId > 0) {
            $city = City::find()->where(['id' => (int)$cityId])->limit(1)->one();

            if (!empty($city)) {
                if (!empty($city['delivery_free_sum'])) {
                    $delivery = $city['delivery_free_sum'];
                }
            }
        }

        return round($delivery);
    }

    public function getDeliveryTextInItem($cityId)
    {
        $delivery = '';

        if ((int)$cityId > 0) {
            $city = City::find()->where(['id' => (int)$cityId])->limit(1)->one();

            // Если включен checkbox "Отключить "Доставку курьером" и оставить самовывоз"
            if (!empty($city['isOnlyPickup']) && $city['isOnlyPickup'] == 1) {
                $delivery = 'Из вашего города только самовывоз.';
            }
            // Если поле "Бесплатная доставка курьером при стоимости от" не пусто
            elseif (!empty($city['delivery_free_sum'])) {
                $delivery = 'Бесплатная доставка при заказе от <b>' .
                    number_format($city['delivery_free_sum'], 0, '', ' ') . ' тг.</b>' .
                    ($cityId == 1 ? ', самовывоз бесплатно' : '') . '';
            }
            // Если поле "Стоимость доставки (общая)" не пусто
            elseif (!empty($city['price_delivery'])) {
                $delivery = 'Стоимость доставки в ваш город <b>' . number_format($city['price_delivery'], 0, '', ' ') . ' тг</b>' .
                    ($cityId == 1 ? ', самовывоз бесплатно' : '') . '';
                // Если поле "Стоимость доставки за 1 кг" не пусто
            } elseif (!empty($city['delivery_weight_sum']) ) {
                $delivery = 'Если за 1 кг, в ваш город доставка <b>' . number_format($city['delivery_weight_sum'], 0, '', ' ') . ' тг за 1 кг.</b>' . ($cityId == 1 ? ', самовывоз бесплатно' : '') . '';
            }
            else {
                $delivery = \Yii::$app->settings->get('price_delivery');
                $price_delivery = (\Yii::$app->settings->get('price_delivery') != '') ? \Yii::$app->settings->get('price_delivery') : 0;

                if ($delivery > 0) {
                    $delivery = 'Стоимость доставки в ваш город <b>' . number_format($price_delivery, 0, '', ' ') . ' тг</b>' . ($cityId == 1 ? ', самовывоз бесплатно' : '') . '';
                }
                else {
                    $min_price_delivery = (\Yii::$app->settings->get('min_price_delivery') != '') ? \Yii::$app->settings->get('min_price_delivery') : 0;
                    $delivery = 'Бесплатная доставка при заказе от <b>'.
                        number_format($min_price_delivery, 0, '', ' ') . ' тг.</b>' .
                        ($cityId == 1 ? ', самовывоз бесплатно' : '') . '';
                }
            }

            // Если включен checkbox "Есть Yandex доставка"
            if(!empty($city['isYandexDelivery'])){
                $delivery .= ', Яндекс Курьер до двери от <b>1000 тг</b>';
            }
        }

        return $delivery;
    }

    /**
     * @param $cityId
     * @return string
     * @deprecated
     */
    public function getDeliveryTextInItem_old($cityId)
    {
        $delivery = '';

        if ((int)$cityId > 0) {
            $city = City::find()->where(['id' => (int)$cityId])->limit(1)->one();

            if (!empty($city['isOnlyPickup']) && $city['isOnlyPickup'] == 1) {
                $delivery = '<p>Из вашего города только самовывоз.</p>';
            }
            elseif (!empty($city['delivery_free_sum'])) {
                $delivery = '<p>Бесплатная доставка при заказе от <b>'.
                    number_format($city['delivery_free_sum'], 0, '', ' ').' тг.</b>'.
                    ($cityId == 1 ? ', самовывоз бесплатно' : '').'</p>';
            }
            elseif (!empty($city['price_delivery'])) {
                $delivery = '<p>Стоимость доставки в ваш город <b>'.number_format($city['price_delivery'], 0, '', ' ').' тг</b>'.
                    ($cityId == 1 ? ', самовывоз бесплатно' : '').'</p>';
            } elseif (!empty($city['delivery_weight_sum']) ) {
                $delivery = '<p>Если за 1 кг, в ваш город доставка <b>'.number_format($city['delivery_weight_sum'], 0, '', ' ').' тг за 1 кг.</b>'.($cityId == 1 ? ', самовывоз бесплатно' : '').'</p>';
            }
            else {
                $delivery = \Yii::$app->settings->get('price_delivery');

                if ($delivery > 0) {
                    $delivery = '<p>Стоимость доставки в ваш город <b>' . number_format(\Yii::$app->settings->get('price_delivery'), 0, '', ' ') . ' тг</b>'.($cityId == 1 ? ', самовывоз бесплатно' : '').'</p>';
                }
                else {
                    $delivery = '<p>Бесплатная доставка при заказе от <b>'.
                        number_format(\Yii::$app->settings->get('min_price_delivery'), 0, '', ' ').' тг.</b>'.
                        ($cityId == 1 ? ', самовывоз бесплатно' : '').'</p>';
                }
            }
        }

        return $delivery;
    }
}
