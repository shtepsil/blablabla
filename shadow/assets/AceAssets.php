<?php
/**
 * Created by PhpStorm.
 * User: lxShaDoWxl
 * Date: 24.04.15
 * Time: 11:37
 */

namespace shadow\assets;


use yii\web\AssetBundle;

class AceAssets extends AssetBundle {
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@bower/ace-builds/src-noconflict';
	/**
	 * @inheritdoc
	 */
	public $js = [
		'ace.js'
	];
	public function init()
	{
		if (!YII_DEBUG) {
			$this->sourcePath = str_replace("ace-builds/src", "ace-builds/src-min", $this->sourcePath);
		}
	}
	/**
	 * @param \yii\web\View $view
	 * @param array $extensions
	 * @return static
	 */
	public static function register($view, $extensions = [])
	{
		$bundle = parent::register($view);
		foreach ($extensions as $_ext) {
			$view->registerJsFile($bundle->baseUrl . "/ext-{$_ext}.js", ['depends' => [static::className()]], "ACE_EXT_" . $_ext);
		}
		return $bundle;
	}
}