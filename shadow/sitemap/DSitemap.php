<?php

namespace shadow\sitemap;

use DOMDocument;
use Yii;
use yii\db\ActiveRecord;

/**
 * Created by PhpStorm.
 * User: askar
 * Date: 14.02.14
 * Time: 21:39
 */
class DSitemap {
    const ALWAYS = 'always';
    const HOURLY = 'hourly';
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    const MONTHLY = 'monthly';
    const YEARLY = 'yearly';
    const NEVER = 'never';

    protected $items = array();

    /**
     * @param        $url
     * @param string $changeFreq
     * @param float  $priority
     * @param int    $lastmod
     */
    public function addUrl( $url, $changeFreq = self::DAILY, $priority = 0.5, $lastMod = 0 ) {
        $host = Yii::$app->request->hostInfo;
        $item = array(
            'loc' => $host . $url,
            'changefreq' => $changeFreq,
            'priority' => $priority
        );
        if( $lastMod ) {
            $item['lastmod'] = $this->dateToW3C( $lastMod );
        }

        $this->items[] = $item;
    }

    /**
     * @param ActiveRecord[] $models
     * @param string          $changeFreq
     * @param float           $priority
     */
    public function addModels( $models, $changeFreq = self::DAILY, $priority = 0.5 ) {
        $host = Yii::$app->request->hostInfo;
        $item = array(
            'loc' => $host . $models->getUrl(),
            'changefreq' => $changeFreq,
            'priority' => $priority
        );

        $this->items[] = $item;
    }

    /**
     * @param ActiveRecord[] $models
     * @param string          $changeFreq
     * @param float           $priority
     */
    public function addItemOld( $url, $changeFreq = self::DAILY, $priority = 0.5 ) {
        $host = Yii::$app->request->hostInfo;
        $item = array(
            'loc' => $host . $url,
            'changefreq' => $changeFreq,
            'priority' => $priority
        );
        $this->items[] = $item;
    }

    /**
     * @param ActiveRecord[] $models
     * @param SitemapItem          $item
     */
    public function addItem( $item ) {
        $host = Yii::$app->request->hostInfo;
        $item->loc = $host.$item->loc;
        if ($item->imageLoc) {
            $item->imageLoc = $host.$item->imageLoc;
        }

        $this->items[] = $item;
    }

    /**
     * @return string XML code
     */
    public function render() {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $urlset = $dom->createElement( 'urlset' );
        $urlset->setAttribute( 'xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9' );
        $urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $urlset->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        $urlset->setAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');
        foreach( $this->items as $item ) {
            $url = $dom->createElement( 'url' );

            if ($item->loc) {
                $elem = $dom->createElement( 'loc' );
                $elem->appendChild( $dom->createTextNode( $item->loc ) );
                $url->appendChild( $elem );
            }

            if ($item->changefreq) {
                $elem = $dom->createElement( 'changefreq' );
                $elem->appendChild( $dom->createTextNode( $item->changefreq ) );
                $url->appendChild( $elem );
            }

            if ($item->priority) {
                $elem = $dom->createElement( 'priority' );
                $elem->appendChild( $dom->createTextNode( $item->priority ) );
                $url->appendChild( $elem );
            }

            if ($item->imageLoc) {
                $imageElem = $dom->createElement("image:image");

                $imageLoc = $dom->createElement( "image:loc" );
                $imageLoc->appendChild($dom->createTextNode( $item->imageLoc ));
                $imageElem->appendChild($imageLoc);

                if ($item->imageCaption) {
                    $imageCaption = $dom->createElement( "image:caption" );
                    $imageCaption->appendChild($dom->createTextNode( $item->imageCaption));
                    $imageElem->appendChild($imageCaption);
                }

                if ($item->imageTitle) {
                    $imageTitle = $dom->createElement( "image:title" );
                    $imageTitle->appendChild($dom->createTextNode( $item->imageTitle));
                    $imageElem->appendChild($imageTitle);
                }


                $url->appendChild($imageElem);
            }



            $urlset->appendChild( $url );
        }
        $dom->appendChild( $urlset );

        return $dom->saveXML();
    }

    protected function dateToW3C( $date ) {
        if( is_int( $date ) ) {
            return date( DATE_W3C, $date );
        } else {
            return date( DATE_W3C, strtotime( $date ) );
        }
    }
}