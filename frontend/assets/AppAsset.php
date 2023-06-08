<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@frontend/assets/main';
    /**
     * @inheritdoc
     */
    public $js = [
        'js/plugins/jquery.placeholder.js?v=1',
        'js/plugins/chosen.jquery.min.js',
        'js/pretty-print-json.min.js',
        'js/plugins/owl.carousel.min.js',
        'js/libs/jquery.mCustomScrollbar.concat.min.js',
        'js/function.js',
        'js/build.js',
        'js/plugins/pop_up.js',
        'js/custom.js',
		
    ];
    /**
     * @inheritdoc
     */
    public $css = [
//        'css/icons/fontawesome/css/all.css',
        'css/pretty-print-json.css',
        'css/style.css',
        'css/custom.css',
    ];
    /**
     * @inheritdoc
     */
    public $depends = [
        'frontend\assets\IeAsset',
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
    ];
}
