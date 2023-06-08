<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
namespace backend\assets;

use yii\web\AssetBundle;

/**
 * author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AdminAsset extends AssetBundle
{
    public $sourcePath = '@webroot/theme';
    public $basePath = '@webroot/theme';
    //	public $baseUrl = '@web/pixel-admin';
    public $css = [
        'css/common.css?v=2',
        //        'css/select-multiple.css',
        'css/pretty-print-json.css',
        'css/custom-checkboxes.css',
        //		'css/widgets.min.css',
//		'css/rtl.min.css',
//		'css/themes.min.css',
//		'css/pages.css',
        'css/custom.css',
        'css/test.css',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_END];

    public function init()
    {
        parent::init();
        $this->js = [
            //            'js/select-multiple.js',
            'js/pretty-print-json.min.js',
            'js/common.js',
            'js/functions.js',
            'js/pixel-admin.js',
            'js/bootbox.js',
            'js/shadow.js',
        ];
    }
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'backend\assets\FontAwesomeAsset',
    ];
}
;
;