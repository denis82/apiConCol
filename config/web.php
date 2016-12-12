<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'parsers' => [
                    'application/json' => 'yii\web\JsonParser',
            ],
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'uefuk94jfwrwwjrmxiewwoozakiwel452wx93llsssfwe872809jelwkrj',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'enableSession' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        
        'urlManager' => [
			
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                /*['class' => 'yii\rest\UrlRule',
                'controller' => 'userperson',
                'extraPatterns' => [
                        
                    ],
                ],*/
                
                '<controller>' => '<controller>/index',
                '<controller>/<category>/<action><suffix:[/]{0,1}>' => '<controller>/<category><action>',
                //'<controller>/<category>/<action>' =>'<controller>/<action>',
// 		'api/profile/registration' =>'api/registration',
// 		'api/profile/updatepassword' =>'api/updatepassword',
// 		'api/profile/login' =>'api/login',
// 		'api/profile/logout' =>'api/logout',
//                 'api/profile/checkeventregistration' =>'api/checkeventregistration',
//                 'api/profile/events' =>'api/events',
//                 'api/profile/update' => 'api/update',
//                 'api/profile/person' => 'api/person',
//                 'api/event/profileRegistration' => 'api/profileregistration',
//                 'api/event/noneprofileregistration' => 'api/noneprofileregistration',
//                 'api/userperson' => 'api/userperson',
//                 'api/userperson/events' => 'api/events',
					'<controller>/<action>' => '<controller>/<action>',
                
            ],
        ],
        
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
