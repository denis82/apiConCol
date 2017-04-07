<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'extensions' => require(__DIR__ . '/../vendor/yiisoft/extensions.php'),
    'bootstrap' => ['log'],
    'language' => 'ru',
    'aliases' => [
        '@imgHost' => 'http://con-col.picom.su',
    ],
//     'sourceLanguage' => 'en-US',
    'components' => [
        'response' => [                 
            'format' => yii\web\Response::FORMAT_JSON, 
                            'charset' => 'UTF-8',               
            ],
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
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@app/runtime/logs/eauth.log',
                    'categories' => array('nodge\eauth\*'),
                    'logVars' => array(),
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
//                 'list' => 'list/listindex',
//                 'catalog' => 'catalog/catalogindex',
//                 'data' => 'data/dataindex',
//                 'album' => 'album/albumindex',
                '<controller>' => '<controller>/index',

                
                '<controller>/<category>/<action><suffix:[/]{0,1}>' => '<controller>/<category><action>',
                '<controller>/<category1>/<category2>/<action><suffix:[/]{0,1}>' => '<controller>/<category1><category2><action>',
                '<controller>/<action>' => '<controller>/<action>',
                
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
                'eauth' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@eauth/messages',
                ],
            ],
       ],
       'eauth' => require(__DIR__ . '/eauth.php'),

       'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
            //'class' => 'Swift_SmtpTransport',
            //'host' => 'localhost',
            //'username' => 'picomsu',
            //'password' => 'NyAIh9kH',
            //'port' => '587',
            //'encryption' => 'tls',
        ],
       ],
       
        'authClientCollection' => [
                'class' => 'yii\authclient\Collection',
                'clients' => [
                    'google' => [
                        'class' => 'yii\authclient\clients\GoogleOAuth',
                        'clientId' => 'google_client_id',
                        'clientSecret' => 'google_client_secret',
                    ],
                    'twitter' => [
                        'class' => 'yii\authclient\clients\Twitter',
                        'consumerKey' => 'twitter_consumer_key',
                        'consumerSecret' => 'twitter_consumer_secret',
                    ],
                ],
        ],
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
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
