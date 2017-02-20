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

class DataController extends MainapiController
{  
	public function actionDataindex()
    {
    
		$tempArray = [];
		$infotype = Yii::$app->request->post(self::INFOTYPE);
		$ids = $this->simpleArray(Yii::$app->request->post(self::IDS));
		if($ids or $infotype == 'my') {
			$list = new Person;
			switch($infotype){
				case 'expert':
					$this->tempArray = $list->dataLP($ids);  // список экспертов dataListExpert
					break;
				case 'event':
					$this->tempArray = $list->dataLE($ids); // список событий  dataListEvent
					break;
				case 'company':
					$this->tempArray = $list->dataLC($ids); // список компаний  dataListCompany
					break;
				case 'about':
					$this->tempArray = $list->dataLA(); // Страница о программе  dataListAbout
					break;
				case 'resource':
					$this->tempArray = $list->dataLR($ids); // список ресурсов(презентации к мероприятию)  dataListResourse
					break;
				case 'person':
					$this->tempArray = $list->dataLP($ids); // список персон  dataListPerson
					break;
				case 'my':
					$ids = [];
					if(!$idUser = Yii::$app->user->isGuest){
						$ids[] = Yii::$app->user->identity->getId();
					} 
					$this->tempArray = $list->dataLP($ids); // мой список   dataListMy
					break;
				default:
					break;	
			}		
		}	

		if(!empty($this->tempArray)) {$this->datas['success'] = true;}
		$this->checkAuth();
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
	}
}