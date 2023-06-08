<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class ModalPluginAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@frontend/assets/main';
    /**
     * @inheritdoc
     */
    public $js = [
        'js/jquery-extends.js',

    ];
    /**
     * @inheritdoc
     */
    public $css = [
        'css/plugins/modal.css',
    ];
    /**
     * @inheritdoc
     */
    public $depends = [
//        'frontend\assets\IeAsset',
//        'yii\web\YiiAsset',
//        'yii\web\JqueryAsset',
    ];

}//Class