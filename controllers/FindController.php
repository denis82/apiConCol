<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Event;
use app\models\Person;
use app\models\Listing;
use app\models\Company;
use app\models\Find;
use yii\base\DynamicModel;

/*	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/	В каком порядке могут приходить данные на сервер	
/!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/	ACTION 					INFOTYPE					RESULT
/	-						expert						эксперты 
/	-						event						События верхнего уровня
/	-						company						компании
/!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/	company					person						сотрудники компании
/!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/	event					expert						эксперты мероприятия
/	event					company						компании мероприятия
/	event					person						участники мероприятия
/!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/	person					event						мероприятия на которые зарегистрировался пользователь
/	person					company						компании пользователя
/!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/	my						company						мои компании
/	my						event						события на которые я зарегистрирован
/	my						person						мои знакомые персоны (те которых я добавил в свой список)
/!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ 
/ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ 
*/

class FindController extends MainapiController
{
	public function actionFindindex()
    {
		$tempArray = [];
		$infotype = Yii::$app->request->post(self::INFOTYPE);
		$action = Yii::$app->request->post(self::ACTION);
		$ids = $this->simpleArray(Yii::$app->request->post(self::IDS));
		if(!in_array($action,['person','event','company','my'])) { // если ACTION пуст
			$list = new Find;
			switch($infotype){
				case 'expert':
					$this->tempArray = $list->findLP();  // список персон
					break;
				case 'event':
					$this->tempArray = $list->findLE(); // список событий
					break;
				case 'company':
					$this->tempArray = $list->findLC(); // список компаний
					break;
				default:
					break;	
			}			
		} else {	//  если ACTION не пуст используется функция-дракон "listOfSomething" из класса родителя CommonLDC()
			$modelList = new Find;
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