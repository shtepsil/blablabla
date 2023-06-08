<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@webroot/theme/css/libs/font-awesome-4.7.0';
    public $basePath = '@webroot/theme/css/libs/font-awesome-4.7.0';
    public $css = [
        'css/font-awesome.css',
    ];
}
;;