<?php

namespace app\controllers;

use Yii;
use yii\BaseYii;
use yii\db\Query;
use app\models\User;
use app\models\Login;
use app\models\Signup;
use app\models\Person;
use app\models\UserEdit; 
use yii\rest\Controller;
use yii\db\ActiveRecord;
use yii\base\DynamicModel;
use app\models\Userprofile;
use app\models\Phonemaildata;
use app\models\EventSubscription;
use yii\filters\auth\HttpBearerAuth;


class EventController extends MainapiController
{

	const REGISTRATION = 'registration';
	
	public $temp = [];
    
	public function actionProfileregistration()
	{
		$idUser = Yii::$app->user->identity->getId();
		if($idUser) {
			$state = Yii::$app->request->post('state');
			$this->temp = $this->simpleArray(Yii::$app->request->post(self::IDS));
			if($this->temp) {  // если полученный массив ids[] валидный едем дальше
				$events = new EventSubscription;
				$this->tempArray = $events->status($idUser,$this->temp,$state); 
				if(!empty($this->tempArray)) {$this->datas['success'] = true;} 
			} else {
				$this->datas['errors'][] = 'данные массива ids не корректные';
			}
			//$this->datas['authorized'] = true;
		} /*else {
			$this->datas['authorized'] = false;
		}*/
		$this->checkAuth();
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
	}
	
	
	public function actionNoneprofileregistration()
    {
		
// 		try {
// 			 Yii::$app->user->identity->getId();
// 		} catch (Exception $e){
// 			$idUser = true;
// 		}
		//if(!$idUser) {
			//$state = Yii::$app->request->post('state');
			$values = Yii::$app->request->post();
			$this->temp = $this->simpleArray(Yii::$app->request->post(self::IDS));
			if(!empty($this->temp)) {
				$person = new Person();
				$person->attributes = $values;
				if($person->validate()) {
					if($person->save()) {
						$events = new EventSubscription;
						$this->tempArray = $events->status($person->id,$this->temp,self::REGISTRATION);
						if(!empty($this->tempArray)) {$this->datas['success'] = true;} 
					}
				}
				
			}
			//$this->datas['authorized'] = false;
// 		} else {
// 			$this->datas[self::DATAS] = [];
// 			$this->datas['success'] = false;
// 			//$this->datas['authorized'] = true;
// 		}
		$this->checkAuth();
		$this->datas[self::DATAS] = $this->tempArray;

		return $this->datas; 
	}
}