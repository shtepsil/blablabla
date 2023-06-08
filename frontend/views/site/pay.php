<?php

use common\models\Orders; 
use Yii;
$order_model = Orders::findOne($id);
$sum_real=$order_model->realSum();
$publicId=Yii::$app->params['cloudpayments']['public_id'];
?>
<script src="https://widget.cloudpayments.ru/bundles/cloudpayments"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script>

$(document).ready(pay);

 function pay() { 
 var widget = new cp.CloudPayments();
    widget.pay('auth', // или 'charge'
        { //options			  
		publicId : '<?=$publicId?>', //id из личного кабинета
		description : 'Оплата заказа на сайте kingfisher.kz', //назначение
		amount : <?=$sum_real?>, //сумма
		currency : 'KZT', //валюта
		invoiceId : '<?=$id?>', //номер заказа  (необязательно)
		accountId : '<?=$order_model->user_id?>', //идентификатор плательщика (необязательно)
		email:'<?=$order_model->user_mail?>'	
        },
        {
            onSuccess: function (options) { // success
                //действие при успешной оплате
            },
            onFail: function (reason, options) { // fail
                //действие при неуспешной оплате
            },
            onComplete: function (paymentResult, options) { //Вызывается как только виджет получает от api.cloudpayments ответ с результатом транзакции.
                //например вызов вашей аналитики Facebook Pixel
            }
        }
    )
};
</script>