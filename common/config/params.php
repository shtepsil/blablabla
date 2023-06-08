<?php

$params = [
    'siteName' => 'kingfisher',
	'siteNameWithDomain'=>'Kingfisher.kz',
    'user.passwordResetTokenExpire' => 3600,
    'currency' => 'KZT',
	'bearer' => 'AQAAAABduIjLAAVM1d1UoOp6r0_NruKkifi1oAk',
    'oneSignal' => [
        'app_id' => '8409cf5f-4c42-4960-b4fa-b3f1dc2f85fe',
        'rest_api_key' => 'ZjFkYWI1NmMtMDEyYi00YTEyLWI5OTQtMDgyMTlkZjZlOWY5',
        'user_auth_key' => 'Mjg1Yjg1OWMtMzQ5NS00Mjk4LThjNmItYzc5OWMwNzQyYTgy',
        'templates' => [
            'bonus_add' => '429666db-af33-4636-92d1-f43ad6b41247'
        ]
    ]
];

if(isset($_SERVER['HTTP_HOST'])){
    switch($_SERVER['HTTP_HOST']){
        case 'kingfisher.kz':
            $params['adminEmail'] = 'boris.topkosov@gmail.com';
            $params['supportEmail'] = 'info@kingfisher.kz';
            $params['cloudpayments'] = [
                'public_id' => 'pk_428f20909a10049cbc7f52db25672',
                'api_key' => 'eeb81e433dff887f872ca5c90f612e80'
            ];
            break;
        case 'test.kingfisher.kz':
            $params['adminEmail'] = 'test@site.com';
            $params['supportEmail'] = 'test@site.kz';
            $params['cloudpayments'] = [
                'public_id' => 'pk_59654463e7a4a02b6eabd4415141a',
                'api_key' => 'fc114dc8b02a894a79cb50f3dbae6633'
            ];
            break;
        default:
            $params['adminEmail'] = 'test@site.com';
            $params['supportEmail'] = 'test@site.kz';
            $params['cloudpayments'] = [
                'public_id' => 'pk_59654463e7a4a02b6eabd4415141a',
                'api_key' => 'fc114dc8b02a894a79cb50f3dbae6633'
            ];
    }
}

return $params;
