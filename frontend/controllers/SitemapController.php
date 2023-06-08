<?php

namespace frontend\controllers;

use backend\interfaces\Clickable;
use backend\models\Module;
use backend\models\Pages;
use common\models\Brands;
use common\models\Category;
use common\models\Items;
//use common\models\Articles;
use common\models\Recipes;
use shadow\sitemap\DSitemap;
use shadow\sitemap\SitemapItem;
use Yii;
use yii\rbac\Item;
use yii\web\Controller;
use yii\web\Response;

class SitemapController extends Controller {

    /**
     *
     */
    public function actionXml() {

        $sitemap = new DSitemap();


        $this->index($sitemap);
        $this->modules($sitemap);
        $this->category($sitemap);
        $this->items($sitemap);
        $this->recipes($sitemap);
        //$this->brands($sitemap);
//        $this->articles($sitemap);
        $this->pages($sitemap);



        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/xml');

        $xml = $sitemap->render();
        return $xml;
    }

    /**
     * @param $sitemap DSitemap
     */
    private function index($sitemap) {
        $sitemapItem = new SitemapItem();
        $sitemapItem->loc = "/";
        $sitemapItem->changefreq = DSitemap::ALWAYS;
        $sitemapItem->priority = 1;

        $sitemap->addItem($sitemapItem);
    }

    /**
     * @param $sitemap DSitemap
     */
    private function category($sitemap) {
        /**
         * @var $items Category[]
         */
        $items = Yii::$app->cache->get('categories');
        if ($items == null) {
            $items = Category::find()->andWhere(['isVisible'=>1])->all();
            Yii::$app->cache->set('categories', $items);
        }

        foreach ($items as $item) {
            $priority = 0.4;
            if ($item->parent_id == null) {
                $priority = 0.8;
            }

            $sitemapItem = new SitemapItem();
            $sitemapItem->loc = $item->url();
            $sitemapItem->changefreq = DSitemap::ALWAYS;
            $sitemapItem->priority = $priority;

            $sitemap->addItem($sitemapItem);
        }
    }

    /**
     * @param $sitemap DSitemap
     */
    private function items($sitemap) {
        /**
         * @var $items Clickable[]
         */
        $items = Yii::$app->cache->get('instruments');
        if ($items == null) {
            $items = Items::find()->where(['isVisible'=>1, 'isDeleted'=>0])->andWhere('price>0')->all();
            Yii::$app->cache->set('instruments', $items);
        }

        foreach ($items as $item) {
            $priority = 0.4;

            $sitemapItem = new SitemapItem();
            $sitemapItem->loc = $item->url();
            $sitemapItem->changefreq = DSitemap::ALWAYS;
            $sitemapItem->priority = $priority;

            $image = $item->img(true, 'mini');
            if ($image) {
                $sitemapItem->imageLoc = $image;
                $sitemapItem->imageTitle = $item->name;
                $sitemapItem->imageCaption = $item->name." | Интернет-магазин kingfisher";
            }

            $sitemap->addItem($sitemapItem);
        }
    }

    /**
     * @param $sitemap DSitemap
     */
    private function recipes($sitemap) {
        /**
         * @var $recipes Clickable[]
         */
        $recipes = Yii::$app->cache->get('recipes');
        $recipes = null;
        if ($recipes == null) {
            $recipes_obj = Recipes::find()->where(['isVisible'=>1]);
            $recipes = $recipes_obj->all();
            Yii::$app->cache->set('recipes', $recipes);
        }
        foreach ($recipes as $item) {
            $priority = 0.4;

            $sitemapItem = new SitemapItem();
            $sitemapItem->loc = $item->url();
            $sitemapItem->changefreq = DSitemap::ALWAYS;
            $sitemapItem->priority = $priority;

            $image = $item->img(true, 'mini_list_recipe');
            if ($image) {
                $sitemapItem->imageLoc = $image;
                $sitemapItem->imageTitle = $item->name;
                $sitemapItem->imageCaption = $item->name." | Интернет-магазин kingfisher";
            }

            $sitemap->addItem($sitemapItem);
        }
    }

    /**
     * @param $sitemap DSitemap
     */
//    private function articles($sitemap) {
//        /**
//         * @var $items Clickable[]
//         */
//        $items = Articles::find()->andWhere(['isVisible'=>1])->orderBy('date_created DESC')->all();
//
//        foreach ($items as $item) {
//            $priority = 0.4;
//
//            $sitemapItem = new SitemapItem();
//            $sitemapItem->loc = $item->url();
//            $sitemapItem->changefreq = DSitemap::ALWAYS;
//            $sitemapItem->priority = $priority;
//
//            $sitemap->addItem($sitemapItem);
//        }
//    }

    /**
     * @param $sitemap DSitemap
     */
    private function brands($sitemap) {
        /**
         * @var $items Clickable[]
         */
        $items = Brands::find()->andWhere(['isVisible' => 1])->orderBy(['name' => SORT_ASC])->all();

        foreach ($items as $item) {
            $priority = 0.6;

            $sitemapItem = new SitemapItem();
            $sitemapItem->loc = $item->url();
            $sitemapItem->changefreq = DSitemap::ALWAYS;
            $sitemapItem->priority = $priority;

            $sitemap->addItem($sitemapItem);
        }
    }

    /**
     * @param $sitemap DSitemap
     */
    private function pages($sitemap) {
        /**
         * @var $items Clickable[]
         */
        $items = Pages::find()->andWhere(['isVisible' => 1])->all();

        foreach ($items as $item) {
            $priority = 0.4;

            $sitemapItem = new SitemapItem();
            $sitemapItem->loc = $item->url();
            $sitemapItem->changefreq = DSitemap::ALWAYS;
            $sitemapItem->priority = $priority;

            $sitemap->addItem($sitemapItem);
        }
    }

    /**
     * @param $sitemap DSitemap
     */
    private function modules($sitemap) {
        /**
         * @var $items Module[]
         */
        $items = Module::find()->all();

        foreach ($items as $item) {
            $priority = 0.4;
            if ($item->id == 8) {
                $priority = 1;
            }
            $sitemapItem = new SitemapItem();
            $sitemapItem->loc = $item->url();
            $sitemapItem->changefreq = DSitemap::ALWAYS;
            $sitemapItem->priority = $priority;

            $sitemap->addItem($sitemapItem);
        }
    }


}//Class