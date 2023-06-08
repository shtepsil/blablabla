<?php
namespace shadow\sitemap;

class SitemapItem {
    public $loc;
    public $changefreq = DSitemap::DAILY;
    public $priority;

    public $imageLoc;
    public $imageCaption;
    public $imageTitle;
}
