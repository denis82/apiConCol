<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\Phonemaildata;

class Listing extends CommonLDC
{
    
    public function listLP($ids)
	{
		$listObj = Expert::find()->groupBy('idPerson')->all();
		$tempArray = [];
		if($listObj) {
			foreach($listObj as $string) {
					$tempArray[] = $string['idPerson'];
			}
		} 
		return $tempArray;
	}
    
	public function PersonCompany($ids)
    {
		$listPerson = Person::findOne($ids); 
		if($listPerson) {
			$list = [];
			foreach($listPerson->companys as $per) {
				$list[] = $per['id'];
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
			foreach($listPerson->eventSubscriptions as $per) {
				$list[] = $per['id'];
			}
			return $list;
		} else {
			return $list = [];
		}
    }
    
    public function EventPerson($ids)
    {
		$listPerson = Event::findOne($ids);
		if($listPerson) {
			$list = [];
			foreach($listPerson->eventSubscriptions as $per) {
				$list[] = $per['id'];
			}
			return $list;
		} else {
			return $list = [];
		}
    }
    
    
    public function EventCompany($ids)
    {
// 		$listPerson = Event::findOne($ids);
// 		if($listPerson) {
// 			$list = [];
// 			foreach($listPerson->eventSubscriptions as $per) {
// 				$list[] = $per['id'];
// 			}
// 			return $list;
// 		} else {
// 			return $list = [];
// 		}
		return $list = [];
    }
    
    public function EventExpert($ids)
    {
		return $list = [];
    }
    
    public function CompanyPerson($ids)
    {
		$listPerson = Company::findOne($ids); 
		if($listPerson) {
			$list = [];
			foreach($listPerson->persons as $per) {
				$list[] = $per['id'];
			}
			return $list;
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
				$list[] = $per['id'];
			}
			return $list;
		} else {
			return $list= [];
		}
    }
    
    public function MyEvent($ids)
    {
 		$listEvent = Person::findOne($ids); 
		if($listEvent) {
			$list = [];
			foreach($listEvent->eventSubscriptions as $per) {
				$list[] = $per['idEvent'];
			}
			return $list;
		} else {
			return $list= [];
		}
    }
    
    public function MyPerson($ids)
    {
		return $list= [];
    }
}