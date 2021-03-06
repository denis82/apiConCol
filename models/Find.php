<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\Phonemaildata;
use app\models\TrustedUsers;
use yii\base\Model;

class Find extends CommonLDC
{
	const NOTRUST = 0; 
 
 
	public function findLP() {
		$listObj = Expert::find()->groupBy('idPerson')->all();
		if($listObj) {
			$list = [];
			foreach($listObj as $per) {
				$tempArray = [];
				$tempArray['id'] = $per['idPerson'];
				$tempArray['image'] = (count($per->persons))?$per->persons[0]['photo']:'';
				$tempArray['info'] = (count($per->persons))?$per->persons[0]['descr']:'';
				$tempArray['name'] = (count($per->persons))?$per->persons[0]['name']:'';
 				$tempArray['date'] = 0;
				$tempArray['hint'] = '';
				$tempArray['kind'] = '';
				$list[] = $tempArray;
			}
			return $list;
		} else {
			return $list = [];
		}
	}
	
	public function findLE() {
	
	}
	
	public function findLC() {
	
	}
	public function PersonCompany($ids)
    {
		
		$listPerson = Person::findOne($ids); 
		if($listPerson) {
			$list = [];
			foreach($listPerson->companys as $key => $per) {
				$list['id'] = $per['company_id'];
				$list['name'] = $per['company_name'];
				$list['info'] = '';
				$list['image'] = '';
				$list['date'] = 0;
				$list['hint'] = '';
				$list['kind'] = '';
			}
			return $list;
		} else {
			return $list = [];
		}
    }
    public function PersonEvent($ids)
    {
    
		$listPerson = Person::findOne($ids); 
		if($listPerson) {
			$list = [];
			foreach($listPerson->events as $key => $per) {
				$tempArray['id'] = $per['event_id'];
				$tempArray['name'] = $per['event_name'];
				$tempArray['info'] = $per['event_anons'];
				$tempArray['image'] = $per['event_image'];
				$tempArray['date'] = strtotime($per['event_date']);
				$tempArray['hint'] = '';
				$tempArray['kind'] = '';
				$list[] = $tempArray;
			}
			return $list;
		} else {
			return $list = [];
		}
    }
    
    public function EventPerson($ids)
    {
		$listPerson = Event::find()->where(['event_id' => $ids])->with('eventSubscriptions.persons')->one();
		if($listPerson) {
			$list = [];
			foreach($listPerson->eventSubscriptions as $per) {
				$tempArray = [];
				$tempArray['id'] = $per['idUser'];
				if($per->persons) {
					foreach($per->persons as $key => $res) {
						if($per['idUser'] == $res['id']) {
							$tempArray['image'] = $res['photo'];
							$tempArray['info'] = $res['descr'];
							$tempArray['name'] = $res['name'];
						} 
					}
				} else {
					$tempArray['image'] = '';
					$tempArray['info'] = '';
					$tempArray['name'] = '';
				}
				$tempArray['date'] = 0;
				$tempArray['hint'] = '';
				$tempArray['kind'] = '';
				$list[] = $tempArray;
			}
			return $list ;
		} else {
			return $list = [];
		}
    }

    
    public function EventExpert($ids)
    {
		$listExpert = Event::find()->where(['event_id' => $ids])->with('experts.persons')->one();
		if($listExpert) {
			$list = [];
			foreach($listExpert->experts as $per) {
				$tempArray = [];
				$tempArray['id'] = $per['idPerson'];
				if($per->persons) {
					foreach($per->persons as $key => $res) {
						if($per['idPerson'] == $res['id']) {
							$tempArray['image'] = $res['photo'];
							$tempArray['info'] = $res['descr'];
							$tempArray['name'] = $res['name'];
						} 
					}
				} else {
					$tempArray['image'] = '';
					$tempArray['info'] = '';
					$tempArray['name'] = '';
				}
				$tempArray['date'] = 0;
				$tempArray['hint'] = '';
				$tempArray['kind'] = '';
				$list[] = $tempArray;
			}
			return  $list ;
		} else {
			return $list = [];
		}
    }
    
    public function CompanyPerson($ids)
    {
		$listPerson = Company::find()->where(['company_id' => $ids])->with('companyPersons.persons')->one();
		if($listPerson) {
			$list = [];
			foreach($listPerson->companyPersons as $per) {
				$tempArray = [];
				$tempArray['id'] = $per['idPerson'];
				if($per->persons) {
					foreach($per->persons as $key => $res) {
						if($per['idPerson'] == $res['id']) {
							$tempArray['image'] = $res['photo'];
							$tempArray['info'] = $res['descr'];
							$tempArray['name'] = $res['name'];
						} 
					}
				} else {
					$tempArray['image'] = '';
					$tempArray['info'] = '';
					$tempArray['name'] = '';
				}
				$tempArray['date'] = 0;
				$tempArray['hint'] = '';
				$tempArray['kind'] = '';
				$list[] = $tempArray;
			}
			return $list ;
		} else {
			return $list = [];
		}
		//return $ids ;
    }
    
    public function MyCompany($ids)
    {
 		$listCompany = Person::findOne($ids); 
		if($listCompany) {
			$list = [];
			foreach($listCompany->companys as $per) {
				$tempArray = [];
				$tempArray['id'] = $per['company_id'];
				$tempArray['image'] = $per['company_logo'];
				$tempArray['info'] = '';
				$tempArray['name'] = $per['company_name'];
				$tempArray['date'] = 0;
				$tempArray['hint'] = '';
				$tempArray['kind'] = '';
				$list[] = $tempArray;
			}
			return $list;
		} else {
			return $list= [];
		}
    }

    public function MyPerson($ids)
    {
		$listPerosn = TrustedUsers::find()->where(['idPerson' => $ids])->orWhere(['idPersonTrust' => $ids])->andWhere(['access' => 2])->all();
		$tempArray = [];
		foreach($listPerosn as $person) {
			if(self::NOTRUST != $person->access) {
				$tempArray[] = ($ids == $person['idPersonTrust'])?$person['idPerson']:$person['idPersonTrust'];
			}	
		}
		$tempArray = array_unique($tempArray);
		$listPerson = Person::findAll($tempArray);
		if($listPerson) {
			$list = [];
			foreach($listPerson as $per) {
				$tempArray = [];
				$tempArray['id'] = $per['id'];
				$tempArray['image'] = $per['photo'];
				$tempArray['info'] = $per['descr'];
				$tempArray['name'] = $per['name'];
				$tempArray['date'] = 0;
				$tempArray['hint'] = '';
				$tempArray['kind'] = '';
				$list[] = $tempArray;
			}
			return $list;
		} else {
			return $list= [];
		}
    }
}