<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Event;
use app\models\Person;
use app\models\Listing;
use app\models\Company;
use app\models\Catalog;
use yii\base\DynamicModel;

class CatalogController extends MainapiController
{
	public function actionCatalogindex()
    {
		$tempArray = [];
		$infotype = Yii::$app->request->post(self::INFOTYPE);
		$action = Yii::$app->request->post(self::ACTION);
		$ids = $this->simpleArray(Yii::$app->request->post(self::IDS));
		if(!in_array($action,['person','event','company','my'])) {
		
			$list = new Person;
			switch($infotype){
				case 'expert':
					$this->tempArray = $list->catalogLP();  // список персон
					break;
				case 'event':
					$this->tempArray = $list->catalogLE(); // список событий
					break;
				case 'company':
					$this->tempArray = $list->catalogLC(); // список компаний
					break;
				default:
					break;	
			}			
		} else {			
			$modelList = new Catalog;
			if(in_array($infotype,['person','event','company','expert'])) {
				if($ids) {
					
					$this->tempArray = $modelList->listOfSomething($action,$infotype,array_shift ($ids)); 
					// если ид есть то он единственный, ид не обязательный
				} else {
					if('my' === $action and Yii::$app->request->cookies->getValue('token', false)) { // можно улучшить
						$access_token = Yii::$app->request->cookies->getValue('token', false);
						$user = User::findOne(['access_token' => explode(' ',$access_token)[1]]);
						$ids = [];
						if($user) {
							$ids[] = $user->id;
							//$this->datas['id'] = $access_token;
						}
					}
					$this->tempArray = $modelList->listOfSomething($action,$infotype,array_shift ($ids));
				}
			}
		}
		if(!empty($this->tempArray)) {$this->datas['success'] = true;}
		$this->checkAuth();
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
	}
}
// 					$listObj = $list->listPerson();
// 					if($listObj) {
// 						foreach($listObj as $string) {
// 							$tempArray['fields'] = $string->phonemaildatas;
// 							foreach($string->phonemaildatas as $keys => $fields) {
// 								foreach($fields as $key => $field) {
// 									$tempArray['fields'][$keys][$key] = $field;
// 								}
// 							}
// 							$arr = [];
// 							$arr['fields'] = $tempArray;
// 							foreach($string as $key=>$str) {
// 								$arr[$key] = $str;
// 							}
// 							$this->tempArray[] = $arr;
// 						}
// 					}