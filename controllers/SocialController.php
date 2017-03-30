<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\Social;
use yii\helpers\VarDumper;
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
                'only' => array('login'),
            ),
        );
    }
    
    
    
    public function init(){
        parent::init();
        $this->optionalActions = ['registration','login'];
    }
    
    public function actionRegistration() 
    {
        $serviceName = Yii::$app->getRequest()->post($this->network);
        if (isset($serviceName)) {
            
            $eauth = Yii::$app->get('eauth')->getIdentity($serviceName);            
            $_GET['code'] = Yii::$app->getRequest()->post('code');
            $modelSocial = new Social;
            $modelSocial->login = Yii::$app->getRequest()->post('login');
            $this->datas['des'] = $eauth->authenticate();
            try {
//                 if ($eauth->authenticate()) {
//                 
//                     $identity = $modelSocial->socialRegistration($eauth);
//                     if ($identity) {
//                         $this->datas['success'] = true;
//                     }
//                 }
            }
            catch (\nodge\eauth\ErrorException $e) {
                Yii::$app->getSession()->setFlash('error', 'EAuthException: '.$e->getMessage());
            }
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