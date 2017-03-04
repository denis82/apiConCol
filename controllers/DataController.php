<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Data;
use app\models\Event;
use app\models\Person;
use app\models\Listing;
use app\models\Company;
use app\models\Catalog;
use yii\base\DynamicModel;

/*	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/	В каком порядке могут приходить данные на сервер	
/!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/	INFOTYPE					RESULT
/	expert						Информация об эксперте
/	event						Информация о событии
/	company						Информация о компании
/	person						Информация о персоне
/	resource					Информация об ресурсах (презентации к мероприятию)
/	my							Мой профиль
/	about						Страница о программе
/!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ 
/ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ 
*/

class DataController extends MainapiController
{  
	public function actionDataindex()
    {
    
		$tempArray = [];
		$infotype = Yii::$app->request->post(self::INFOTYPE);
		$ids = $this->simpleArray(Yii::$app->request->post(self::IDS));
		if($ids or $infotype == 'my') {
			$modelData = new Data;
			switch($infotype){
				case 'expert':
					$this->tempArray = $modelData->dataListPerson($ids,$infotype);  // список экспертов dataListExpert
					break;
				case 'event':
					$this->tempArray = $modelData->dataListEvent($ids,$infotype); // список событий  dataListEvent
					break;
				case 'company':
					
					$this->tempArray = $modelData->dataListCompany($ids,$infotype); // список компаний  dataListCompany
					break;
				case 'about':
					$this->tempArray = $modelData->dataListAbout(); // Страница о программе  dataListAbout
					break;
				case 'resource':
					$this->tempArray = $modelData->dataListResource($ids,$infotype); // список ресурсов(презентации к мероприятию)  dataListResourse
					break;
				case 'person':
					$this->tempArray = $modelData->dataListPerson($ids,$infotype); // список персон  dataListPerson
					break;
				case 'my':
					$ids = [];
					if(!$idUser = Yii::$app->user->isGuest){
						$ids[] = Yii::$app->user->identity->getId();
					} 
					$this->tempArray = $modelData->dataListPerson($ids,$infotype); // мой список   dataListMy
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