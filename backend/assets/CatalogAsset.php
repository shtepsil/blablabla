<?php
/**
 * Created by PhpStorm.
 * Project: morkovka
 * User: lxShaDoWxl
 * Date: 28.07.15
 * Time: 11:34
 */
namespace backend\assets;

use yii\web\AssetBundle;

class CatalogAsset extends AssetBundle
{
    public $sourcePath = '@webroot/theme';
    public $basePath = '@webroot/theme';
    public $css = [
        'css/catalog.css',
    ];
    public $js = [
		'js/highcharts/core.js',
		'js/highcharts/charts.js',
		'js/highcharts/animated.js',
		'js/highcharts/highcharts.js',
		'js/highcharts/highcharts3d.js',
		'js/highcharts/cylinder.js',
		'js/highcharts/funnel3d.js',
		'js/highcharts/pyramid3d.js',
		'js/highcharts/exporting.js',
		'js/highcharts/export-data.js',
		'js/highcharts/accessibility.js',
		'js/highcharts/highcharts-more.js'
    ];
    public $depends = [
        'backend\assets\AdminAsset',
    ];
}