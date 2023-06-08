<?php
// namespace app\components;

// class Bootstrap implements BootstrapInterface
// {
// 	public function bootstrap($app)
// 	{
// 		if (preg_match("|^/about.html|", $_SERVER['REQUEST_URI'])) {
// 			Yii::$app->response->redirect('/about', 301)->send();
// 			Yii::$app->end();
// 			return;
// 		}
// 	}

// 	public function init()
// 	{
// 		if (preg_match("|^/about.html|", $_SERVER['REQUEST_URI'])) {
// 			Yii::$app->response->redirect('/about', 301)->send();
// 			Yii::$app->end();
// 			return;
// 		}
// 	}
// }