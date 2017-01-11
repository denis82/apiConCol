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
	public $tempArray = [];
	
	
	
	public $_userId = false;
	
	public function behaviors()
    {
        //$behaviors = parent::behaviors();
        $behaviors = [];
        $behaviors['authenticator']['class'] = HttpBearerAuth::className();
        
        $behaviors['authenticator']['except'] = ['registration','login'];
        //$behaviors = array_merge($behaviors, parent::behaviors());
        //var_dump($behaviors); die();
        return $behaviors;
    }
    
    public function simpleArray($array)
    {
		$temp = [];
		if (null != $array and is_array($array)) {
			foreach ($array as $key => $id) {  // в цикле валидируются входные данные
				if (!is_array($id)) {
					if((int)$id) {
						$temp[] =(int)$id;
					}
				}
			}
        } else {
			$temp = [];
        }
		return $temp;
    }
    public function getUserId() {
		if($this->_userId === false)
		{
			$this->_userId = Yii::$app->user->identity->getId();
		}
		return $this->_userId;
    }


}