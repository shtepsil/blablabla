<?php
/**
 * Created by PhpStorm.
 * Project: morkovka
 * User: lxShaDoWxl
 * Date: 28.08.15
 * Time: 12:43
 */
namespace frontend\components;

use common\components\Debugger as d;
use frontend\widgets\ActiveForm;
use Yii;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class SendFormAction extends Action
{
    public $forms = [];
    public function run($f)
    {
        /**
         * @var \yii\base\Model | \frontend\form\Registration $form
         */
        $post = Yii::$app->request->post();
//        d::ajax($post);
        $forms = $this->forms;
        if (isset($forms[$f])) {
            $form = Yii::createObject('frontend\form\\' . $forms[$f]);
//            d::ajax($form);
            if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
                $result = [];
                Yii::$app->response->format = Response::FORMAT_JSON;
                $errors = ActiveForm::validate($form);
                if ($errors) {
                    $result['errors'] = $errors;
                } else {
                    $result = $form->send();
//                    d::ajax($result);
                    if ($result == true) {
                        $result['success'] = true;
                    }
                }
                return $result;
            } else {
                throw new BadRequestHttpException('not found', 404);
            }
        } else {
            throw new BadRequestHttpException('not found', 404);
        }
    }
}