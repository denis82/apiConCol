<?php

namespace app\controllers;

use Yii;


class SocialController extends MainapiController
{

    private $network = 'network'

    public function actionRegistration() 
    {
        $serviceName = Yii::$app->getRequest()->post($this->network);

        if (isset($serviceName)) {
            /** @var $eauth \nodge\eauth\ServiceBase */
            $eauth = Yii::$app->get('eauth')->getIdentity($serviceName);
            $eauth->setRedirectUrl(Yii::$app->getUser()->getReturnUrl());
            $eauth->setCancelUrl(Yii::$app->getUrlManager()->createAbsoluteUrl('site/login'));
            try {
                if ($eauth->authenticate()) {
                    
//                  var_dump($eauth->getIsAuthenticated(), $eauth->getAttributes()); exit;
                    $identity = User::findByEAuth($eauth);
                    
                    Yii::$app->getUser()->login($identity);
                    // special redirect with closing popup window
                    //$eauth->redirect();
                }
                else {
                    // close popup window and redirect to cancelUrl
                    $eauth->cancel();
                }
            }
            catch (\nodge\eauth\ErrorException $e) {
                // save error to show it later
                Yii::$app->getSession()->setFlash('error', 'EAuthException: '.$e->getMessage());

                // close popup window and redirect to cancelUrl
//              $eauth->cancel();
                $eauth->redirect($eauth->getCancelUrl());
            }
        }

        $model = new LoginForm();
        if ($model->load($_POST) && $model->login()) {
            return $this->goBack();
        }
        else {
            return $this->render('login', array(
                'model' => $model,
            ));
        }
    }
    
    public function actionLogin() 
    {
        
        $serviceName = Yii::$app->getRequest()->getQueryParam('service');

        if (isset($serviceName)) {
            /** @var $eauth \nodge\eauth\ServiceBase */
            $eauth = Yii::$app->get('eauth')->getIdentity($serviceName);
            $eauth->setRedirectUrl(Yii::$app->getUser()->getReturnUrl());
            $eauth->setCancelUrl(Yii::$app->getUrlManager()->createAbsoluteUrl('site/login'));
            try {
                if ($eauth->authenticate()) {
                    
//                  var_dump($eauth->getIsAuthenticated(), $eauth->getAttributes()); exit;
                    $identity = User::findByEAuth($eauth);
                    
                    Yii::$app->getUser()->login($identity);
                    // special redirect with closing popup window
                    //$eauth->redirect();
                }
                else {
                    // close popup window and redirect to cancelUrl
                    $eauth->cancel();
                }
            }
            catch (\nodge\eauth\ErrorException $e) {
                // save error to show it later
                Yii::$app->getSession()->setFlash('error', 'EAuthException: '.$e->getMessage());

                // close popup window and redirect to cancelUrl
//              $eauth->cancel();
                $eauth->redirect($eauth->getCancelUrl());
            }
        }

        $model = new LoginForm();
        if ($model->load($_POST) && $model->login()) {
            return $this->goBack();
        }
        else {
            return $this->render('login', array(
                'model' => $model,
            ));
        }
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
    public function actionNotificationtoken() 
    {
    
    }
    public function actionDeletenotificationtoken() 
    {
    
    }
    
}