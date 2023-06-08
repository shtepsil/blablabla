<?php

namespace frontend\form;
use common\components\Debugger as d;
use common\models\User;
use frontend\components\SmsController;
use Yii;
use yii\base\Model;


class SmsLogin extends Model
{

    public $phone;


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
        return 'sms_login';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
            [['phone'],'match','pattern' => '/^((\+?7)(\(?\d{3})\)-?)?(\d{3})(-?\d{4})$/','message'=>Yii::t('main','Некорректный формат поля {attribute}')]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'phone' => 'Телефон',
        ];
    }
    public function send()
    {
	
            $user = User::checkPhone($this->phone,[
                'username'=>($this->phone),
                'isEntity'=>0,
            ]); 

			$result['js'] = <<<JS
				$("#input").css('display','none');
				$("#resmslogin").css('display','none');
				$("#smsform").css('display','none');
				$("#codeform").css('display','block');
				$('.control-label').css('display', 'none');
				$("#getsms").css('display','none');
				$("#resmsnew").css('display','block');
				var target_date = new Date().getTime() + (1*2499*48); 
				var minutes, seconds; 
				var countdown = document.getElementById("tiles"); 
				$("#countdown").css('display','block');
				getCountdown(); 
				var timer = setInterval(function () { getCountdown(); }, 1000);
				function getCountdown(){
					var current_date = new Date().getTime();
					var seconds_left = (target_date - current_date) / 1000; 
					minutes = pad( parseInt(seconds_left / 60) );
					seconds = pad( parseInt( seconds_left % 60 ) );
					if (seconds == 0 && minutes == 0) {
						$('#show').css('display', 'block');
						$('#countdown').css('display', 'none');
						$('#resmslogin').css('display', 'block');
						$("#smsform").css('display','block');		
						$('.control-label').css('display', 'none');	
						clearInterval(timer);
					}
					countdown.innerHTML = "<p style='color:#80858d; font-size:15px; font:1.5em/1.33em 'Proxima Nova',sans-serif'>Код выслан на Ваш номер.<br> Получить новый код можно через <span>" + minutes + "</span>:<span>" + seconds + "</span></p><br>"; 
				}
				function pad(n) {
					return (n < 10 ? '0' : '') + n;
				}
JS;
		
		$code = rand(1000, 9999);
		\Yii::$app->session->set('code', $code);
		\Yii::$app->session->set('phone', $this->phone);
		$phone = $this->phone;


		$result_ = SmsController::send_sms("$phone", "Код: $code. Никому не сообщайте.kingfisher.kz");
        if (TEST_MOBILE) {
            d::td($code);
            $result['message']['success'] = 'Смс ' . $code . ' отправлена на Ваш телефон!!!';
        }else{
            if (count($result_) == 4) {
                $result['message']['success'] = 'Смс отправлена на Ваш номер: ' . $phone;
            } else {
                $result['message']['error'] = 'Произошла ошибка на стороне серверa!';
            }
        }
	
        return $result;
    }	
}