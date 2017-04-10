<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\Social;
use yii\helpers\VarDumper;
use app\models\Photo;
//use \Reflection;
//use \nodge\eauth\openid\ControllerBehavior;

//use \nodge\eauth\openid\ControllerBehavior;

class SocialController extends MainapiController
{

    private $network = 'network';

    public function actions()
    {
        return array(
            'error' => array(
                'class' => 'yii\web\ErrorAction',
            ),
        );
    }
    
    public function behaviors() {
        return array(
            'access' => array(
                'class' => AccessControl::className(),
                'only' => array('login'),
                'rules' => array(
                    array(
                        'allow' => true,
//                      'roles' => array('?'),
                    ),
                    array(
                        'allow' => false,
                        'denyCallback' => array($this, 'goHome'),
                    ),
                ),
            ),
            'eauth' => array(
                // required to disable csrf validation on OpenID requests
                'class' => \nodge\eauth\openid\ControllerBehavior::className(),
                'only' => array('login,registration'),
            ),
        );
    }
    
    
    
    public function init(){
        parent::init();
        $this->optionalActions = ['registration','login'];
    }
    
//     public function actionRegistration() 
//     {
//         $serviceName = Yii::$app->getRequest()->post($this->network);
//         $modelSocial = new Social;
//         $modelSocial->login
//         $q = Yii::$app->getRequest()->post();
//         $arrMy = [];
//         foreach($q as $key => $res) {
//             if(!is_array($res)) {
//             $arrMy[$key] =  $res;
//             }
//             if(is_array($res)) {
//             $arrMy[$key] =  json_encode($res);
//             }
//         }
//         $q = json_encode($arrMy);
//         if (isset($serviceName)) {
//             
//             $eauth = Yii::$app->get('eauth')->getIdentity($serviceName); 
//             $eauth->setRedirectUrl(Yii::$app->getUser()->getReturnUrl());
//             $eauth->setCancelUrl(Yii::$app->getUrlManager()->createAbsoluteUrl('social/registration'));
//             $_GET['code'] = Yii::$app->getRequest()->post('code');
//             $modelSocial = new Social;
//             $modelSocial->login = Yii::$app->getRequest()->post('login');
//             //$ref = new \ReflectionClass($eauth);
// //             $test = new Photo();
// //                 $test->code = $q;
// //                 $test->save();
//                 
//                 $CLIENT_ID = '871507680263-6g2sjenb3nidsj0apr74nscpi64i19vd.apps.googleusercontent.com';
//        $client = new \Google_Client(['client_id' => $CLIENT_ID]);
//         $payload = $client->verifyIdToken($_POST['code']);
//         if ($payload) {
//         //$userid = $payload['sub'];
//         $test = new Photo();
//         $test->code = json_encode($payload);
//         $test->save();
//         } else {
//        // $userid = $payload['sub'];
//         $test = new Photo();
//         $test->code = json_encode($payload);
//         $test->save();
//         }
//             //$this->datas['des'] = $eauth->authenticate();
//             
// //             try {
// //                 if ($eauth->authenticate()) {
// //                 
// //                     $identity = $modelSocial->socialRegistration($eauth);
// //                     if ($identity) {
// //                         $this->datas['success'] = true;
// //                     }
// //                 }
// //             }
// //             catch (\nodge\eauth\ErrorException $e) {
// //                 Yii::$app->getSession()->setFlash('error', 'EAuthException: '.$e->getMessage());
// //             }
//         }
//         
//         $this->checkAuth();
//         //$this->datas['sd'] = $services;
//         return $this->datas;
//     }
    
    
    public function actionRegistration() 
    {
        $serviceName = Yii::$app->getRequest()->post($this->network);
        $test = new Photo();
                    $test->code = $serviceName;
                    $test->save();
        $code = Yii::$app->getRequest()->post('code');

        if (isset($serviceName)) {
           
            
            
            switch ($serviceName) {
                case 'vkontakte':
                     $ch = curl_init();
                    
                    // установка URL и других необходимых параметров
                    curl_setopt($ch, CURLOPT_URL, "https://oauth.vk.com/access_token?client_id=5949715&client_secret=cy04orm3YTFnwKycGR5S&grant_type=client_credentials");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    
                    
                    // загрузка страницы и выдача её браузеру
                    $token = curl_exec($ch);
                    curl_close($ch);
                    $tok = json_decode($token); 
                    
                    $chq = curl_init();

                    // установка URL и других необходимых параметров
                    curl_setopt($chq, CURLOPT_URL, "https://api.vk.com/method/secure.checkToken");
                    curl_setopt($chq, CURLOPT_POSTFIELDS,"token=".$code."&client_secret=cy04orm3YTFnwKycGR5S&access_token=".$tok->access_token);
                    
                    curl_setopt($chq, CURLOPT_HEADER, 0);
                    curl_setopt($chq, CURLOPT_RETURNTRANSFER, 1);
                    // загрузка страницы и выдача её браузеру
                    $Preview = curl_exec($chq);
                    curl_close($chq);

                    //echo '<pre>'; var_dump($Preview); echo '</pre>';
                    //echo '<pre>'; print_r($tok->access_token); 
                    
                    break;
                case 'google_oauth':
                    break;
            }
            
            
            
            $eauth = Yii::$app->get('eauth')->getIdentity($serviceName); 
            $eauth->setRedirectUrl(Yii::$app->getUser()->getReturnUrl());
            $eauth->setCancelUrl(Yii::$app->getUrlManager()->createAbsoluteUrl('social/registration'));
            $_GET['code'] = Yii::$app->getRequest()->post('code');
            $_GET['login'] = Yii::$app->getRequest()->post('login');
            $modelSocial = new Social;
            $modelSocial->login = Yii::$app->getRequest()->post('login');
   
                
       
       
//         $test = new Photo();
//             $test->code = json_encode($this->datas['des']);
//             $test->key = 7;
//             $test->save();
        
//            if($eauth->authenticate()) {
// 
//             $identity = User::findByEAuth($eauth);
//                     
//             }
//             try {
//                 if ($eauth->authenticate()) {
//                 
//                     $identity = $modelSocial->socialRegistration($eauth);
//                     if ($identity) {
//                         $this->datas['success'] = true;
//                     }
//                 }
//             }
//             catch (\nodge\eauth\ErrorException $e) {
//                 Yii::$app->getSession()->setFlash('error', 'EAuthException: '.$e->getMessage());
//             }
        }
        
        $this->checkAuth();
        //$this->datas['sd'] = $services;
        return $this->datas;
    }
    
    
    
    public function actionLogin() 
    {
        $modelSocial = new Social();
        $serviceName = Yii::$app->getRequest()->post($this->network);
        
        if (isset($serviceName)) {
            
            $eauth = Yii::$app->get('eauth')->getIdentity($serviceName);
            $_GET['code'] = Yii::$app->getRequest()->post('code');
            try {
                if ($eauth->authenticate()) {
                    $modelSocial->name = $eauth->getServiceName();
                    $modelSocial->id = $eauth->getId();
                    $identity = $modelSocial->socialLogin($eauth);
                    if ($identity) {
                        $this->datas['success'] = true;
                    }
                }
            }
            catch (\nodge\eauth\ErrorException $e) {
                Yii::$app->getSession()->setFlash('error', 'EAuthException: '.$e->getMessage());
            }
        }
        
        $this->checkAuth();
        return $this->datas;
    }
    
    public function actionConnect() 
    {
    
    }
    public function actionDisconnect() 
    {
    
    }
    public function actionUdatePassword() 
    {
    
    }
    
}