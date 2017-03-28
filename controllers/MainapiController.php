<?php

namespace app\controllers;

use Yii;
use yii\BaseYii;
use yii\db\Query;
use app\models\User;
use yii\rest\Controller;
use app\behaviors\MyBehavior;
use app\filters\BodyParamAuth;

class MainapiController  extends Controller
{
	public $tempArray = [];
	public $datas = ['success' => false]; 				// успешность операции по умолчанию false
	public static $authorized;							// статус авторизации (true/false)
	public $_userId = false;
	public $optionalActions = [];     //	массив опциональных экшенов для авторизации 
	
	
	const IDS = 'ids';
	const DATAS = 'datas';
	const ACTION = 'action';
	const INFOTYPE = 'infotype';

	/*
	/	реализация аутентификации по токену (HTTP Bearer token). Для авторизации  нужно  токен получить из куков 
	/	логика описана в классе BodyParamAuth по адресу /filters/BodyParamAuth.php
	*/
	
	public function behaviors()
    {
        $behaviors = [];
        $behaviors['authenticator']['class'] = BodyParamAuth::className();        
        $behaviors['authenticator']['except'] = ['registration','login','noneprofileregistration','upload'];
        $behaviors['authenticator']['optional'] = $this->optionalActions;
        return $behaviors;
    }
    

    /*
    /	определяет авторизирован ли пользователь на момент вызова 
    /
    */
    
    public function checkAuth()
    {
		if(Yii::$app->user->isGuest) {
			$this->datas['authorized'] = false;
		} else {
			$this->datas['authorized'] = true;
		}
	}
    
    /*
    /	(возможно велосипед) валидируемый массив на входе должен содержать только int 
    /	любое значение будет приведено к int, если не получится вернет пустой массив
    */
    
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
    
    /*
    /	(возможно понадобится)  тоже что simpleArray() только массив вложенный
    */
    
//     public function checkArray($array) 
//     {
// 		$res = true;
// 		if(is_array($array)) {
// 			foreach ($array as $firstKey => $firstStep) {
// 				if (!is_numeric($firstKey)  ) {
// 					$res = false;
// 					}	
// 				if(is_array($firstStep)) {	
// 					foreach($firstStep as $secondKey => $secondStep) {
// 						
// 						if (!is_numeric($secondStep) || !is_numeric($secondKey) || is_array($secondStep) ) {
// 						$res = false;
// 						}
// 					}
// 				} else {
// 					$res = false;
// 				}
// 			}
// 		} else {
// 			$res = false;
// 		}
// 		return  $res;
//     }

}