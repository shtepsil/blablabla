<?php

namespace frontend\form;

use common\models\SpecActionCode;
use common\models\SpecActionPhone;
use common\models\User;
use yii\base\Model;
use GuzzleHttp\Client as HttpClient;
use yii\helpers\ArrayHelper;

class PromoRegistration extends Model
{
    public $name;
    public $phone;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'trim'],
            [['name', 'phone'], 'required'],
            [
                ['phone'],
                'match',
                'pattern' => '/^((\+?7)(\(?\d{3})\)-?)?(\d{3})(-?\d{4})$/',
                'message' => \Yii::t('main', 'Некорректный формат поля {attribute}')
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'phone' => 'Телефон',
        ];
    }

//    /**
//     * Sends an email to the specified email address using the information collected by this model.
//     *
//     * @param  string  $email the target email address
//     * @return boolean whether the email was sent
//     */
//    public function sendEmail($email)
//    {
//        return Yii::$app->mailer->compose()
//            ->setTo($email)
//            ->setFrom([$this->email => $this->name])
//            ->setSubject($this->subject)
//            ->setTextBody($this->body)
//            ->send();
//    }
    /**
     * @param $specialActionCode SpecActionCode
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send($specialActionCode)
    {
        $specActionPhone = SpecActionPhone::find()->where([
            'phone' => $this->phone,
            'spec_action_code_id' => $specialActionCode->id
        ])->one();
        if (!$specActionPhone) {
            $specActionPhone = new SpecActionPhone();
        }
        if ($specActionPhone->status == 1) {
            return $specActionPhone->id;//уже был использован код на этот QR код
        }
        $send = false;
        if ($specActionPhone->isNewRecord) {
            $send = true;
            $specActionPhone->phone = $this->phone;
            $specActionPhone->name = $this->name;
            $specActionPhone->spec_action_code_id = $specialActionCode->id;
            $specActionPhone->send_time = time();
            $specActionPhone->code = $specActionPhone->generateCode();
            $specActionPhone->status = 0;
            $specActionPhone->save(false);
        } elseif (time() >= ($specActionPhone->send_time + 600)) {
            $send = true;
            $specActionPhone->send_time = time();
            $specActionPhone->save(false);
        }
        if ($send) {
            $this->sendSMS($specActionPhone->phone, 'Код: ' . $specActionPhone->code);

        }
        return $specActionPhone->id;
    }

    const FORMAT_JSON = 3;

    /**
     * @param $phone
     * @param $message
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendSMS($phone, $message)
    {
        if (ArrayHelper::getValue(\Yii::$app->params['smsc'], 'enable') !== true) {
            return false;
        }
        $guzzle = new HttpClient([
            'timeout' => 5,
            'connect_timeout' => 5,
        ]);
        $params = [

        ];
        $base = [
            'charset' => 'utf-8',
            'login' => ArrayHelper::getValue(\Yii::$app->params['smsc'], 'login'),
            'psw' => ArrayHelper::getValue(\Yii::$app->params['smsc'], 'secret'),
            'sender' => '',
            'fmt' => self::FORMAT_JSON,
            'phones' => $phone,
            'mes' => $message,
            'translit' => 1,
        ];
        $params = \array_merge($base, \array_filter($params));
        try {
            $response = $guzzle->request('POST', 'https://smsc.ru/sys/send.php', ['form_params' => $params]);
            $response = json_decode((string)$response->getBody(), true);
            if (isset($response['error'])) {
                throw new \DomainException($response['error'], $response['error_code']);
            }
            return $response;
        } catch (\DomainException $exception) {
            \Yii::error("smsc.ru responded with an error '{$exception->getCode()}: {$exception->getMessage()}'", 'sms');
        } catch (\Exception $exception) {
            \Yii::error("sms with an error '{$exception->getCode()}: {$exception->getMessage()}'", 'sms');
        }
    }
}