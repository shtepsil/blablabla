<?php

namespace apiking\form;

use common\components\retailcrm\ApiHelper;
use common\models\Items;
use common\models\Orders;
use common\models\OrdersHistory;
use common\models\OrdersItems;
use common\models\Sets;
use common\models\User;
use frontend\models\retailcrm\CreateCrmOrder;
use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\helpers\Json;

class CodeLogin extends Model
{

    public $code;
	public $sessioncode;

    /**
     * Returns the form name that this model class should use.
     *
     * The form name is mainly used by [[\yii\widgets\ActiveForm]] to determine how to name
     * the input fields for the attributes in a model. If the form name is "A" and an attribute
     * name is "b", then the corresponding input name would be "A[b]". If the form name is
     * an empty string, then the input name would be "b".
     *
     * By default, this method returns the model class name (without the namespace part)
     * as the form name. You may override it when the model is used in different forms.
     *
     * @return string the form name of this model class.
     */
    public function formName()
    {
        return 'code_login';
    }
  
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
		 [['code'], 'required'],
            [['code'], 'required', 'on' => ['popup']],
            [['code'], 'string', 'max' => 255],
			['code', 'custom_function_validation'],
       ];
    }

	public function custom_function_validation($attribute, $params)
	{
		if($this->$attribute != $this->sessioncode) $this->addError($attribute, 'Не верный код!!!');
	}

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Код',
        ];
    }
    public function send($code, $session_id)
    {
		 		  		  
        $sessioncode = \Yii::$app->session->get($session_id . 'code', []);
		$sessionusername = \Yii::$app->session->get($session_id . 'username', []);
		$session_user_id = \Yii::$app->session->get($session_id . 'user_id', []);
		$user = User::find()->andWhere(['id' => $session_user_id, 'status' => User::STATUS_ACTIVE])->one();
				
		$this->code = $code['code'];
		$this->sessioncode = $sessioncode;

		// Проверка SMS кода (кастомный метод валидации custom_function_validation)
		if (!$this->validate()) {
            return null;
        }  
			
		// if ($sessioncode == $code['code']) {
			
			// if(Yii::$app->user->login($user)){		
				// $result['message']['success'] = $user->auth_key;			
			// }else{
				// $result['message']['error'] = 'Произошла ошибка на стороне сервера!';
				// Yii::$app->response->statusCode = 400;
			// }			
		// } else {
			// $result['message']['error'] = 'Вы ввели не верный код';
			// Yii::$app->response->statusCode = 400;
		// } 

		if ($this->sessioncode == $this->code) {
			//	if ($sessioncode == $code['code']) {
			
			if(Yii::$app->user->login($user)){		
				$result = $user->auth_key;			
			}else{
				$result = null;
			}			  
		} else {
			$result = null;
		} 
		
        return $result;		
    }
	
	 public function sendd($code, $te)
    {
		  		
		$this->code = $code;
		$this->sessioncode = $te;  
		  
		if (!$this->validate()) {
            return null;
        }  	  		 

		if ($this->sessioncode == $this->code) {
								
			$result ='l';			
					  
		} else {
			$result = null;
		} 		
        return $result;		
    }	
}