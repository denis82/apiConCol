<?php

namespace app\models;

use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\TrustedUsers;
use app\models\Phonemaildata;

class CommonLDC extends Model
{ 
    public function listOfSomething($action,$infotype,$ids = 0)
    {	
		switch($action){
			case 'person':
				switch($infotype){
					case 'company':
						$list = $this->PersonCompany($ids);// компании пользователя
						break;
					case 'event':
						$list = $this->PersonEvent($ids); // мероприятия на которые зарегистрировался пользователь
						break;
					default:
						break;
				}
				break;
			case 'event':
				switch($infotype){
					case 'expert':
						$list = $this->EventExpert($ids); // эксперты мероприятия
						break;
					case 'company':
						$list = $this->EventCompany($ids); // ?
						break;
					case 'person':
						$list = $this->EventPerson($ids); // участники мероприятия
						break;
					default:
						break;
				}
				break;
			case 'company':
				switch($infotype){
					case 'person':
						
						$list = $this->CompanyPerson($ids); // сотрудники компании
						break;
					default:
						break;
				}
			break;
			case 'my':
				switch($infotype){
					case 'person':
						$list = $this->MyPerson($ids); // мои знакомые персоны (те которых я добавил в свой список)
						break;
					case 'company':
						$list = $this->MyCompany($ids); // мои компании
						break;
					case 'event':
						$list = $this->PersonEvent($ids); // события на которые я зарегистрирован
						break;	
					default:
						break;
				}
			break;
		}
		return $list;
	}
	
}