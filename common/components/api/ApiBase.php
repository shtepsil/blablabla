<?php

namespace common\components\api;

use common\components\Debugger as d;
use Yii;
use common\components\api\CException;

class ApiBase
{
    public $token;
    public $client;
    public $params;
    public $config;
    public $api_url;

    public function __construct($api_url, $token = '')
    {
        $this->client = new CcUrl(['base_url' => $this->api_url]);
    }

    protected function request($endpoint = '', $params = [], $method = 'GET')
    {
//        d::ajax($endpoint);
//        d::ajax($params);
        $params['base_headers'] = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];
        if($endpoint != '') $endpoint = '/' . $endpoint;
        $response = $this->client->request($endpoint, $params, $method);
//        d::ajax($response);

//        $arr_response = json_decode($response, true);

        $arr_response = json_decode($response['data'], true);
        if(!is_array($arr_response)){
            $arr_response = [ 'response' => $arr_response ];
        }
        if(d::$view_response) {
            $arr_response['endpoint'] = $response['endpoint'];
            $arr_response['debug'] = $response['debug'];
        }


        return $arr_response;
    }

}//Class