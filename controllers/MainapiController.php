<?php

namespace app\controllers;

use Yii;
use yii\BaseYii;
use yii\db\Query;
use yii\rest\Controller;
use app\models\User;
use app\filters\BodyParamAuth;
use app\behaviors\MyBehavior;

class MainapiController  extends Controller
{
	public $tempArray = [];
	public $datas = ['success' => false]; // успешность операции по умолчанию false
	public static $authorized;
	public $_userId = false;
	
	const IDS = 'ids';
	const DATAS = 'datas';
	const ACTION = 'action';
	const INFOTYPE = 'infotype';

	
	public function behaviors()
    {
        $behaviors = [];
        $behaviors['authenticator']['class'] = BodyParamAuth::className();        
        $behaviors['authenticator']['except'] = ['registration','login','noneprofileregistration','upload'];
        $behaviors['authenticator']['optional'] = ['listindex','catalogindex','dataindex'];
        return $behaviors;
    }
    

    
    public function checkAuth()
    {
		if(Yii::$app->user->isGuest) {
			$this->datas['authorized'] = false;
		} else {
			$this->datas['authorized'] = true;
		}
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
    
    public function checkArray($array) 
    {
		$res = true;
		if(is_array($array)) {
			foreach ($array as $firstKey => $firstStep) {
				if (!is_numeric($firstKey)  ) {
					$res = false;
					}	
				if(is_array($firstStep)) {	
					foreach($firstStep as $secondKey => $secondStep) {
						
						if (!is_numeric($secondStep) || !is_numeric($secondKey) || is_array($secondStep) ) {
						$res = false;
						}
					}
				} else {
					$res = false;
				}
			}
		} else {
			$res = false;
		}
		return  $res;
    }
    
//     public function checkAuthorized() {
// 		if(false == $this->authorized)
// 		{
// 			$loginModel = new Login();
// 			Yii::$app->user->login($loginModel->getUser());
// 			if(Yii::$app->user->identity->access_token) {
// 				$this->authorized = true;
// 			}
// 		}
// 		return $this->authorized;
//     }
// 	
// 	/*
// 	*
// 	*
// 	*/
// 	public function checkField($type,$field = false) {
// 		if($field)
// 		{
// 			return $field;
// 		}
// 		return ;
//     }

}