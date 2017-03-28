<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

return [
    'language' => 'ru-RU',
    'components' => [

        'mailer' => [
            'useFileTransport' => true,
        ],
        'db' => [ 
            'dsn' => 'mysql:host=localhost;dbname=picomsu_concolasite',
        ],
        
        'urlManager' => [
            'showScriptName' => true,
        ],
    ],   
];
