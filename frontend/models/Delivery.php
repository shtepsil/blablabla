<?php

namespace frontend\models;

use common\components\Debugger as d;
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

            // isOnlyPickup - checkbox (Отключить "Доставку курьером" и оставить самовывоз)
            if (!empty($city['isOnlyPickup']) && $city['isOnlyPickup'] == 1) {
                $delivery = -1;
            }

            /*
             * delivery_free_sum - Бесплатная доставка курьером при стоимости от
             * Если итоговая сумма корзины равна или больше 'стоимости от'
             */
            elseif (!empty($city['delivery_free_sum']) && $sum >= $city['delivery_free_sum']) {
                $delivery = 0;

            /*
             * Стоимость доставки (общая)
             * Если стоимость доставки общая указана, то берём эту стоимость.
             */
            } elseif (!empty($city['price_delivery'])) {
                $delivery = $city['price_delivery'];
            /*
             * Стоимость доставки за 1 кг
             * Если сумма для 1кг указана, и вес за 1кг больше нуля,
             * то указанную сумму умножим на вес одного кг
             */
            } elseif (!empty($city['delivery_weight_sum']) && $weight > 0) {
                $delivery = $city['delivery_weight_sum'] * $weight;
            } else {
                /*
                 * В настройках есть суммы, указывающие минимальную сумму корзины
                 * и максимальную сумму корзины
                 */
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
        $pickup = true;

        if ((int)$cityId > 0) {
            $city = City::find()->where(['id' => (int)$cityId])->limit(1)->one();

            // Если включен checkbox "Отключить "Доставку курьером" и оставить самовывоз"
            if (!empty($city['isOnlyPickup']) && $city['isOnlyPickup'] == 1) {
                $delivery = 'Из вашего города только самовывоз.';
                $pickup = false;
            }
            // Если поле "Бесплатная доставка курьером при стоимости от" не пусто
            elseif (!empty($city['delivery_free_sum'])) {
                $delivery = 'Бесплатная доставка при заказе от <b>' .
                    number_format($city['delivery_free_sum'], 0, '', ' ') . ' тг.</b>';
            }
            // Если поле "Стоимость доставки (общая)" не пусто
            elseif (!empty($city['price_delivery'])) {
                $delivery = 'Стоимость доставки в ваш город <b>' . number_format($city['price_delivery'], 0, '', ' ') . ' тг</b>';
            // Если поле "Стоимость доставки за 1 кг" не пусто
            } elseif (!empty($city['delivery_weight_sum']) ) {
                $delivery = 'В ваш город доставка <b>' . number_format($city['delivery_weight_sum'], 0, '', ' ') . ' тг за 1 кг.</b>';
            }
            else {
            // Если все поля пустые

                $delivery = \Yii::$app->settings->get('price_delivery');
                $price_delivery = (\Yii::$app->settings->get('price_delivery') != '') ? \Yii::$app->settings->get('price_delivery') : 0;

                if ($delivery > 0) {
                    $delivery = 'Стоимость доставки в ваш город <b>' . number_format($price_delivery, 0, '', ' ') . ' тг</b>' . ($cityId == 1 ? ', самовывоз бесплатно' : '') . '';
                }
                else {

                    $min_price_delivery = \Yii::$app->settings->get('min_price_delivery');
                    /*
                     * Если "Система->Настройки->вкладка Доставка,
                     * поле минимальная сумма заказа" пусто.
                     */
                    if($min_price_delivery == null) $min_price_delivery = 0;

                    /*
                     * Если в админке "Система->Настройки->вкладка Доставка,
                     * поле минимальная сумма заказа" не пусто.
                     */
                    if($min_price_delivery > 0){
                        $delivery = 'Бесплатная доставка при заказе от <b>'
                            . number_format($min_price_delivery, 0, '', ' ') . ' тг.</b>';
                    }else{
                        $delivery = 'Стоимость доставки в ваш город рассчитывается индивидуально.';
                        $pickup = false;
                    }
                }
            }

            if($pickup AND !empty($city['pickup_switcher']) && $city['pickup_switcher'] == 1){
                $delivery .= ($cityId == 1 ? ', самовывоз бесплатно' : '');
            }

            // Если включен checkbox "Есть Yandex доставка"
            if(!empty($city['isYandexDelivery'])){
                $delivery .= ', Яндекс Курьер до двери от <b>1000 тг</b>';
            }
        }

        return $delivery;
    }

    public function getPickUpPrice($cityId)
    {
        $delivery = 0;

        if ((int)$cityId > 0) {
            $city = City::find()->where(['id' => (int)$cityId])->limit(1)->one();

            if (!empty($city) && !empty($city['pickup_price']) && $city['pickup_price'] > 0) {
                $delivery = $city['pickup_price'];
            }
        }

        return round($delivery);
    }
}
