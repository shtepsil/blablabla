<?php


namespace apiking\components;

use yii\web\Response;
use Yii;

trait ApiResponses
{
    public function getErrorResponse($message, $code, $detail = false, $pointer = false)
    {
        $errorsObject = [];
        $errors = [];

        $errors[] = [
            'title' => $message,
            'status' => (string) $code,
        ];

        if ($detail) {
            $errors[0]['detail'] = $detail;
        }

        if ($pointer) {
            $errors[0]['source']['pointer'] = $pointer;
        }

        $errorsObject['errors'] = $errors;

        return $this->response($errorsObject, $code);
    }

    private function response($message, $code = 200)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->statusCode = $code;
        return $message;
    }
}