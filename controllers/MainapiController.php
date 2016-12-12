<?php

namespace app\controllers;

use Yii;
use yii\BaseYii;
use yii\db\Query;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\BodyParamAuth;
use app\behaviors\MyBehavior;

class MainapiController  extends Controller
{

	public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::className();
        $behaviors['authenticator']['except'] = ['registration','login'];
        return $behaviors;
    }

}