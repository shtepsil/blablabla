<?php
/**
 * Created by PhpStorm.
 * Project: morkovka
 * User: lxShaDoWxl
 * Date: 22.09.15
 * Time: 10:55
 */
namespace shadow\authclient;

class GoogleOAuth extends \yii\authclient\clients\GoogleOAuth
{
    public $apiBaseUrl = 'https://www.googleapis.com/oauth2/v2';

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return $this->api('userinfo', 'GET');
    }
}