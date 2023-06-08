<?php

namespace frontend\controllers;

use common\components\Debugger as d;
use common\models\Items;
use shadow\helpers\StringHelper;
use shadow\plugins\google\XmlFid;
use shadow\plugins\google\XmlFidItem;
use yii\web\Controller;
use DOMDocument;
use Yii;
use yii\web\Response;

class XmlController extends Controller
{

    // Маршрут urlRules сюда направляет пользователя
    public function actionFidGoogleAdwords(){

        $fid = new XmlFid();

        $this->items($fid);

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/xml');

        $xml = $fid->render();
        return $xml;

    }

    /**
     * @param $fid XmlFid
     */
    public function items($fid){

        $items = null;
        if ($items == null) {

            $items_obj = Items::find()->where(['isVisible'=>1, 'googleFid' => 1, 'isDeleted'=>0])->andWhere('price>0');
            $items = $items_obj->all();
        }

        foreach ($items as $item) {
            $fidItem = new XmlFidItem();
            $fidItem->props['id'] = $item->id;
            $fidItem->props['title'] = $item->name;
            $fidItem->props['description'] = StringHelper::clearHtmlString($item->body);
            $fidItem->props['link'] = $item->url();

            $image = $item->img(true, 'page_item');
            if ($image) {
                $fidItem->props['image_link'] = $image;
            }
//            $fidItem->props['sell_on_google_sale_price'] = $item->price;
//            $fidItem->props['sell_on_google_price'] = 'N/A';

            $fidItem->props['price'] = $item->real_price() . ' ' . Yii::$app->params['currency'];
            if($item->old_price){
                $fidItem->props['sale_price'] = $item->real_price() . ' ' . Yii::$app->params['currency'];
                $fidItem->props['price'] = $item->old_price . ' ' . Yii::$app->params['currency'];
            }

            if ($item->measure_price != $item->measure && $item->weight != 1){
                $fidItem->props['price'] = $item->sum_price() . ' ' . Yii::$app->params['currency'];
                if($item->old_price){
                    $fidItem->props['sale_price'] = $item->sum_price() . ' ' . Yii::$app->params['currency'];
                    $fidItem->props['price'] = $item->sum_price(1, 'main', $item->old_price) . ' ' . Yii::$app->params['currency'];
                }
            }

            $fidItem->props['condition'] = 'new';

            if ($item->status){
                $fidItem->props['availability'] = 'in_stock';
            }else{
                $fidItem->props['availability'] = 'out_of_stock';
            }

            $fid->addItem($fidItem);
        }
    }

}//Class