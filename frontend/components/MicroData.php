<?php
/**
 * Created by PhpStorm.
 * User: Сергей
 * Date: 16.07.2020
 * Time: 11:07
 */

namespace frontend\components;

use common\components\Debugger as d;
use common\components\helpers\CHtml;
use shadow\helpers\StringHelper;
use Yii;
use yii\helpers\Html;

class MicroData
{
    public $item;
    public $reviews;

    public function __construct($item = false, $reviews = false)
    {
        if ($item and is_object($item))
            $this->item = $item;
        if ($reviews)
            $this->reviews = $reviews;
    }

    public function product($params = [])
    {

        return [
            'itemscope' => CHtml::crta([
                'itemscope',
                'itemtype' => 'http://schema.org/Product'
            ]),
            'name' => CHtml::crta(['itemprop' => 'name']),
        ];
    }

    public function imageObject($params = [])
    {
        return [
            'itemscope' => CHtml::crta([
                'itemscope',
                'itemtype' => 'http://schema.org/ImageObject'
            ]),
            'meta' =>
            CHtml::meta(['itemprop' => 'name'], $this->item->name) .
            CHtml::meta(
                ['itemprop' => 'description'],
                StringHelper::clearHtmlString($this->item->body_small)
            ),
            'contentUrl' => ['itemprop' => 'contentUrl'],
        ];
    }

    public function rating($params = [])
    {
        return [
            'itemscope' => CHtml::crta([
                'itemprop' => 'reviewRating',
                'itemscope',
                'itemtype' => 'http://schema.org/Rating',
            ]),
        ];
    }

    public function offers($params = [])
    {

        // Если есть просто одна цена
        $md_price = $this->item->real_price();

        // Если есть цена за шт определённого веса
        if ($this->item->measure_price != $this->item->measure && $this->item->weight != 1) {
            // Если есть просто одна цена за шт
            $md_price = $this->item->sum_price();
        }

        return [
            'itemscope' => CHtml::crta([
                'itemprop' => 'offers',
                'itemscope',
                'itemtype' => 'http://schema.org/Offer',
            ]),
            'meta' =>
            CHtml::meta(['itemprop' => 'url'], Yii::$app->request->hostInfo . $_SERVER['REDIRECT_URL']) .
            CHtml::meta(['itemprop' => 'priceCurrency'], 'KZT') .
            CHtml::meta(['itemprop' => 'price'], number_format($md_price, 0, '', '')) .
            CHtml::meta(
                ['itemprop' => 'priceValidUntil'],
                // Текущая дата + сутки
                date('Y-m-d', time() + 86400)
            ),
            'availability' => [
                // CHtml::meta(['itemprop'=>'availability'],'http://schema.org/PreOrder')
                'PreOrder' => $this->getMetaProp('availability', 'http://schema.org/PreOrder'),
                'InStock' => $this->getMetaProp('availability', 'http://schema.org/InStock'),
                'OutOfStock' => $this->getMetaProp('availability', 'http://schema.org/OutOfStock'),
            ],
        ];
    }

    public function review($params = [])
    {
        return [
            'itemscope' => CHtml::crta([
                'itemprop' => 'review',
                'itemscope',
                'itemtype' => 'http://schema.org/Review',
            ]),
        ];
    }

    public function person($params = [])
    {
        return [
            'itemscope' => CHtml::crta([
                'itemprop' => 'author',
                'itemscope',
                'itemtype' => 'http://schema.org/Person',
            ]),
        ];
    }

    public function aggregateRating($params = [])
    {

        if (!empty($this->reviews)) {
            $reviews_count = count($this->reviews);
        } else {
            $reviews_count = 0;
        }

        return [
            'itemscope' => CHtml::crta([
                'itemprop' => 'aggregateRating',
                'itemscope',
                'itemtype' => 'http://schema.org/AggregateRating',
            ]),
            'meta' =>
            CHtml::meta(['itemprop' => 'reviewCount'], $reviews_count) .
            CHtml::meta(['itemprop' => 'ratingValue'], $this->item->popularity),
        ];
    }

    public function breadcrumbs($params = [])
    {
        return [
            'itemscope' => CHtml::crta([
                'itemscope',
                'itemtype' => 'http://schema.org/BreadcrumbList',
            ]),
            'itemlist' => CHtml::crta([
                'itemprop' => 'itemListElement',
                'itemscope',
                'itemtype' => 'http://schema.org/ListItem',
            ]),
            'propLink' => CHtml::crta(['itemprop' => 'item']),
            'propLabel' => CHtml::crta(['itemprop' => 'name']),
        ];
    }

    public function get($name, $data = [])
    {
        $result = '';
        if (method_exists($this, $name)) {
            if (is_string($data)) {
                $result = $this->$name()[$data];
            }

            if (is_array($data)) {
                if (count($data)) {
                    if (count($data) == 1) {
                        $key = key($data);
                        if (is_numeric($key)) {
                            $result = $this->$name()[$data[$key]];
                        } else {
                            /*
                             * Если нужно сделать автоматическое определение
                             * многоуровневости массива $data,
                             * то это нужно делать здесь...
                             */
                            $result = $this->$name()[$key][$data[$key]];
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param $images
     * @return string
     */
    public function getImagesLink($images)
    {
        $paths_img = '';
        if (is_array($images) and count($images)) {
            foreach ($images as $image_path) {
                $paths_img .=
                    CHtml::tag('link', '', ['itemprop' => 'image', 'href' => $image_path]);
            }
        }
        return $paths_img;
    }

    public function getMetaProp($name, $value)
    {
        if ($name and $name != '') {
            return CHtml::meta(['itemprop' => $name], $value);
        } else
            return '';
    }

}