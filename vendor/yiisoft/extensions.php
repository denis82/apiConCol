<?php

$vendorDir = dirname(__DIR__);

return array (
  'yiisoft/yii2-swiftmailer' => 
  array (
    'name' => 'yiisoft/yii2-swiftmailer',
    'version' => '2.0.6.0',
    'alias' => 
    array (
      '@yii/swiftmailer' => $vendorDir . '/yiisoft/yii2-swiftmailer',
    ),
  ),
  'yiisoft/yii2-bootstrap' => 
  array (
    'name' => 'yiisoft/yii2-bootstrap',
    'version' => '2.0.6.0',
    'alias' => 
    array (
      '@yii/bootstrap' => $vendorDir . '/yiisoft/yii2-bootstrap',
    ),
  ),
  'yiisoft/yii2-debug' => 
  array (
    'name' => 'yiisoft/yii2-debug',
    'version' => '2.0.9.0',
    'alias' => 
    array (
      '@yii/debug' => $vendorDir . '/yiisoft/yii2-debug',
    ),
  ),
  'yiisoft/yii2-gii' => 
  array (
    'name' => 'yiisoft/yii2-gii',
    'version' => '2.0.5.0',
    'alias' => 
    array (
      '@yii/gii' => $vendorDir . '/yiisoft/yii2-gii',
    ),
  ),
  'yiisoft/yii2-faker' => 
  array (
    'name' => 'yiisoft/yii2-faker',
    'version' => '2.0.3.0',
    'alias' => 
    array (
      '@yii/faker' => $vendorDir . '/yiisoft/yii2-faker',
    ),
  ),
  'nodge/yii2-eauth' => 
  array (
    'name' => 'nodge/yii2-eauth',
    'version' => '2.4.1.0',
    'alias' => 
    array (
      '@nodge/eauth' => $vendorDir . '/nodge/yii2-eauth/src',
    ),
    'bootstrap' => 'nodge\\eauth\\Bootstrap',
  ),
  'skeeks/yii2-google-api' => 
  array (
    'name' => 'skeeks/yii2-google-api',
    'version' => '1.0.0.0',
    'alias' => 
    array (
      '@skeeks/yii2/googleApi' => $vendorDir . '/skeeks/yii2-google-api',
    ),
  ),
);
