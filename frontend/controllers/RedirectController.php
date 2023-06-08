<?php

namespace frontend\controllers;


use common\models\Category;
use common\models\Items;
use frontend\components\MainController;
use yii;

/**
 * Class SiteController
 * @package frontend\controllers
 * @property \frontend\assets\AppAsset $AppAsset
 */

class RedirectController extends MainController
{
    public function actionItem($id) {
        $item = Items::find()->andWhere(['id'=>$id])->one();
        if ($item) {
            $this->redirect($item->url(), 301);
            return;
        }
        throw new yii\web\NotFoundHttpException();
    }

    public function actionCatalog($id) {
        $item = Category::find()->andWhere(['id'=>$id])->one();
        if ($item) {
            $this->redirect($item->url(), 301);
            return;
        }
        throw new yii\web\NotFoundHttpException();
    }
}
