<?php
/*
 * Ссылка на документацию Kaspi API
 * https://kaspi.kz/merchantcabinet/support/pages/viewpage.action?pageId=22645486
 */

namespace common\components\api;

use common\components\Debugger as d;
use Yii;
//use apiking\Logger;

class Api extends ApiBase
{

    public function __construct($api_url, $token = '')
    {
        if(!$api_url AND $token == ''){
            throw new CException('Ошибка установки параметров');
        }
        $this->api_url = $api_url;
        $this->token = $token;
        parent::__construct($token);
    }

    public function test()
    {
        return $this->request('');
    }






    // =======================================================================
    // OLD OLD OLD OLD OLD OLD OLD OLD OLD OLD OLD OLD OLD OLD OLD OLD OLD OLD
    // =======================================================================

    public function getNodePush($point = '', $params = [], $method = 'GET')
    {
        return $this->request($point, $params, $method);
    }

    public function getPush($point = '', $params = [], $method = 'GET')
    {
        return $this->request($point, $params, $method);
    }

    public function getDataGeojson()
    {
        return $this->request('basketapi/get-data-geojson', [], 'POST');
    }

    /*
     * =============================================
     * NODE ========================================
     * =============================================
     */
    public function actionNodeSms($type = 'send'){
        return $this->request('sms', ['type' => $type]);
    }
    /*
     * =============================================
     * // node =====================================
     * =============================================
     */

    /*
     * =============================================
     * DEBUGS ======================================
     * =============================================
     */
    public function actionDebug($data = []){
        $user_id = $data['data'];
//        $user_id = '19324';
        return $this->request('siteapi/debug', [ 'id' => $user_id ]);
//        d::$d = true;
//        return $this->request('siteapi/debug');
    }
    public function getDebug(){
        return $this->request('siteapi/get-debug', []);
    }
    public function actionDebugSet($data = []){
        $user_id = '16893';
        return $this->request('siteapi/set-debug', [
            'session_id' => $this->token,
            's_set' => 1
        ]);
    }
    public function actionDebugGet($data = []){
        $user_id = '16893';
        $params = [
            'id' => $user_id,
            'session_id' => $this->token,
            's_get' => 1
        ];

        if (
            isset($_POST['data']) AND $_POST['data'] != ''
            AND isset($_POST['input_name'])
            AND $_POST['input_name'] == 'debug_file_name'
        ) {
            $params['debug_file'] = $_POST['data'];
        }

        return $this->request('siteapi/get-debug', $params);
    }
    /*
     * =============================================
     * =============================================
     * =============================================
     */

    public function generateGuest($user = []){
        return $this->request('userapi/generateguest');
    }

    public function userRegister($user = []){
        return $this->request('userapi/register', $user, 'POST');
    }

    public function userDelete($user_id = 1){
        return $this->request('userapi/user-delete', $user_id);
    }

    public function userGet($user_id = 1){
        return $this->request('userapi/get-user', $user_id);
    }

    public function getId($user = []){
        return $this->request('userapi/get-id?session_id=' . $this->token, $user);
    }

    public function userLogin($user = []){
        return $this->request('userapi/login', $user, 'POST');
    }

    public function userLogout($user = []){
        return $this->request('userapi/logout?session_id=' . $this->token, $user);
    }

    // Отправка СМС на телефон
    public function smsLogin($phone = []){
        return $this->request('userapi/smslogin?session_id=' . $this->token, $phone, 'POST');
    }
    // Авторизация по СМС коду
    public function codeLogin($code = []){
        return $this->request('userapi/codelogin?session_id=' . $this->token, $code, 'POST');
    }
    // Авторизация по СМС коду
    public function codeLogin_new($code = []){
        return $this->request('userapi/codelogin?session_id=' . $this->token, $code, 'POST');
    }

    public function getBasket($data = []){
        return $this->request('basketapi/basket', ['session_id' => $this->token]);
    }

    public function addcart($id = ''){
//        d::$d = true;
        if($id == '') return ['error' => 'method->addItemBasket(?) ID not found'];
        // count=$count&id=$id&session_id=$token_guest
        $data = [
            'count' => 1,
            'id' => $id,
//            'session_id' => $this->token
        ];
        return $this->request('basketapi/addcart?session_id=' . $this->token, $data, 'POST');
    }

    public function checkpromo($promo_code = ''){
        if($promo_code == '') return false;

        $params = [
            'code' => $promo_code,
            'key' => $this->token,
            'price' => 10000
        ];
//        d::pe($_SERVER['HTTP_APP_TYPE']);
        if(isset($_SERVER['HTTP_APP_TYPE'])){
            $params['headers'] = [
                'APP-TYPE: ' . $_SERVER['HTTP_APP_TYPE']
            ];
        }

        return $this->request('basketapi/controlcheckpromo', $params);
    }

    public function actions($data = []){

//        $arr = ['one'=>'Раз', 'two'=>'Два'];
//        $s = array_filter($arr, function(){
//            d::pri($ks);
//        }, $ks);
//        if($s) d::pe('Значение найдено');
//        else d::pe('Значение не найдено');

        return $this->request('siteapi/actions', []);
    }
    public function actionSessionClear($data = []){
        return $this->request('userapi/logout');
    }
    public function getSession($data = []){
//        return $this->request('siteapi/get-session?session_id='.$this->token);
        return $this->request('siteapi/get-session?session_id=' . $this->token);
    }

    public function getCities($data = []){
        return $this->request('siteapi/getcities');
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function actionCartClear($data = []){
//        return $this->request('basketapi/delcartallitems?session_id='.$this->token, [], 'POST');
        return $this->request('basketapi/clear-test-session?session_id=' . $this->token);
    }

    /**
     * @param array $data
     * @return mixed
     * // baseURL/apiking/basketapi/checkoutorder?session_id=$token_guest
     */
    public function createOrder($params = []){
//        d::$d = true;
//        d::$c_data = true;
//        d::pe($this->token);
        return $this->request(
            'basketapi/checkoutorder?session_id=' . $this->token
            . '&key=' . $this->token,
            $params,
            'POST'
        );
    }

    /**
     * @param array $data
     * @return mixed
     * // baseURL/apiking/basketapi/checkoutorder?session_id=$token_guest
     */
    public function createOrderYandex($data = []){
//        d::$d = true;
//        d::$c_data = true;
//        d::pe($this->token);
        return $this->request(
            'basketapi/checkoutorder?session_id=' . $this->token
            . '&key=' . $this->token,
            $data,
            'POST'
        );
    }

    /**
     * @param array $data
     * @return mixed
     * // baseURL/apiking/basketapi/checkoutorder?session_id=$token_guest
     */
    public function opt($data = []){
//        d::$d = true;
//        d::$c_data = true;
//        d::pe($this->token);
        return $this->request(
            'siteapi/headcatalog?session_id=' . $this->token
            . '&key=' . $this->token,
            $data
        );
    }

}//Class



















