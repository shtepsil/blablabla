<?php
namespace shadow\plugins\seo;

use Yii;
use yii\base\BaseObject;
use yii\web\UrlRuleInterface;

class SClearUrlRule extends BaseObject implements UrlRuleInterface
{
    /**
     * @inheritdoc
     */
    public function parseRequest($manager, $request){
        $path = $request->getPathInfo();
        $tmpUrl = Yii::$app->seo->correctionUrl($path);
        if ($tmpUrl!=$path) {
            Yii::$app->response->redirect([$tmpUrl], 301);
            Yii::$app->end();
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function createUrl($manager, $route, $params){
        return false;
    }
}