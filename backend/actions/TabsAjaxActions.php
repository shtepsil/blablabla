<?php

namespace backend\actions;

use common\components\Debugger as d;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use Yii;
use yii\web\Response;

class TabsAjaxActions extends Action
{

    public $actions = [];
    public function run($a)
    {
        $post = Yii::$app->request->post();
//        d::ajax($post);
        $actions = $this->actions;
        $result = [];
        if (isset($actions[$a])) {
            $form = Yii::createObject('backend\actions\\' . $actions[$a]);
//            d::ajax($form);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $form->run();
            } else {
                throw new BadRequestHttpException('not found', 404);
            }
        } else {
            throw new BadRequestHttpException('not found', 404);
        }
    }

}//Class