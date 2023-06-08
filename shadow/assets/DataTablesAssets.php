<?php
/**
 * Created by PhpStorm.
 * Project: morkovka
 * User: lxShaDoWxl
 * Date: 09.09.15
 * Time: 10:03
 */
namespace shadow\assets;

use yii\web\AssetBundle;

class DataTablesAssets extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@shadow/js_css_lib/datatables';
    /**
     * @inheritdoc
     */
    public $js = [
        'datatables.js',
        'custom_datatables.js',
        'jquery.dataTables.columnFilter.js',
    ];
    public $js_min=[
        'datatables.min.js',
        'custom_datatables.min.js',
    ];
    public function init()
    {
        if (!YII_DEBUG) {
//            $this->js = $this->js_min;
        }
    }
    public $depends = [
        'backend\assets\AdminAsset',
    ];
}