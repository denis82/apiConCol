<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Event;
use app\models\Person;
use app\models\Listing;
use app\models\Company;
use yii\base\DynamicModel;

class ListController extends MainapiController
{
	public function actionListindex()
    {
		$tempArray = [];
		$infotype = Yii::$app->request->post(self::INFOTYPE);
		$action = Yii::$app->request->post(self::ACTION);
		$ids = $this->simpleArray(Yii::$app->request->post(self::IDS));
		
		if(!in_array($action,['person','event','company','my'])) {  // Если action пустой то выводим только списки инфотипов  
			
			switch($infotype){
				case 'expert':
					$list = new Person;
					$this->tempArray = $list->listLP($ids);
					break;
				case 'event':
					$lists = Event::find()->all();
					foreach($lists as $list) {
						$this->tempArray[] = $list->event_id;
					}
					break;
				case 'company':
					$lists = Company::find()->all();
					foreach($lists as $list) {
						$this->tempArray[] = $list->company_id;
					}
					break;
				default:
					break;	
			}			
		} else {			// если action не пустой то выводим инфотипы в зависимости от action ids[]
			$modelList = new Listing;
			if(in_array($infotype,['person','event','company'])) {
				if($ids) {
					
					$this->tempArray = $modelList->listOfSomething($action,$infotype,array_shift ($ids));
				} else {
					if('my' === $action and Yii::$app->request->cookies->getValue('token', false)) {
						$access_token = Yii::$app->request->cookies->getValue('token', false);
						$user = User::findOne(['access_token' => explode(' ',$access_token)[1]]);
						$ids = [];
						if($user) {
							$ids[] = $user->id;
						}
					}
					$this->tempArray = $modelList->listOfSomething($action,$infotype,$ids);
				}
			}
		}
		if(!empty($this->tempArray)) {$this->datas['success'] = true;}
		$this->checkAuth();
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas; 
	}
}