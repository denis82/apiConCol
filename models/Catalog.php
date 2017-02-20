<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\Phonemaildata;
use app\models\TrustedUsers;
use yii\base\Model;

class Catalog extends CommonLDC
{
	const NOTRUST = 0; 
 
	public function PersonCompany($ids)
    {
		
		$listPerson = Person::findOne($ids); 
		if($listPerson) {
			$list = [];
			foreach($listPerson->companys as $key => $per) {
				$list['id'] = $per['id'];
				$list['name'] = $per['name'];
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
		$listPerson = Person::find()->where(['id' => $ids])->with('eventSubscriptions.events')->one();
		if($listPerson) {
			$list = [];
			foreach($listPerson->eventSubscriptions as $per) {
				$tempArray = [];
				$tempArray['id'] = $per['idEvent'];
				if($per->events) {
					foreach($per->events as $key => $res) {
						if('image' == $key) {
							$tempArray['image'] = $res['image'];
							$tempArray['info'] = $res['info'];
							$tempArray['name'] = $res['name'];
						}
					}
				} else {
					$tempArray['image'] = '';
					$tempArray['info'] = '';
					$tempArray['name'] = $res['name'];
				}
 				$tempArray['date'] = strtotime($per['date']);
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
		$listPerson = Event::find()->where(['id' => $ids])->with('eventSubscriptions.persons')->one();
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
    public function EventCompany($ids)
    {
		//$list = Event::find()->where(['id' => $ids])->with('experts.persons')->one();
// 		if($listPerson) {
// 			$list = [];
// 			foreach($listPerson->experts as $per) {
// 				$tempArray = [];
// 				$tempArray['id'] = $per['idUser'];
// 				if($per->persons) {
// 					foreach($per->persons as $key => $res) {
// 						if($per['idUser'] == $res['id']) {
// 							$tempArray['image'] = $res['photo'];
// 							$tempArray['info'] = $res['descr'];
// 							$tempArray['name'] = $res['name'];
// 						} 
// 					}
// 				} else {
// 					$tempArray['image'] = '';
// 					$tempArray['info'] = '';
// 					$tempArray['name'] = '';
// 				}
// 				$tempArray['date'] = 0;
// 				$tempArray['hint'] = '';
// 				$tempArray['kind'] = '';
// 				$list[] = $tempArray;
// 			}
// 			return $list ;
// 		} else {
// 			return $list = [];
// 		}
		return $list = [];
    }
    
    
    public function EventExpert($ids)
    {
		$listExpert = Event::find()->where(['id' => $ids])->with('experts.persons')->one();
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
			return $list = [1];
		}
    }
    
    public function CompanyPerson($ids)
    {
		$listPerson = Company::find()->where(['id' => $ids])->with('companyPersons.persons')->one();
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
    }
    
    public function MyCompany($ids)
    {
 		$listCompany = Person::findOne($ids); 
		if($listCompany) {
			$list = [];
			foreach($listCompany->companys as $per) {
				$tempArray = [];
				$tempArray['id'] = $per['id'];
				$tempArray['image'] = $per['logo'];
				$tempArray['info'] = '';
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