<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Event;
use app\models\Company;
use yii\db\ActiveRecord;
use app\models\Phonemaildata;

class Listing extends CommonLDC
{
    /**
    * @var array
    */
    public $dataResult = [];
    
    public function infotypeSwitch($infotype,$ids)
    {
        $this->dataResult = [];
        switch($infotype){
            case 'expert':
                $this->dataResult = $this->listLP($ids);
                break;
            case 'event':
                $lists = Event::find()->all();
                foreach($lists as $list) {
                    $this->dataResult[] = $list->event_id;
                }
                break;
            case 'company':
                $lists = Company::find()->all();
                foreach($lists as $list) {
                    $this->dataResult[] = $list->company_id;
                }
                break;
            default:
                break;  
        }   
        return $this->dataResult; 
    }
    
    public function actionInfotypeSwitch($infotype,$action,$ids)
    {
        if(in_array($infotype,['person','event','company'])) {
                
                if($ids) {
                    
                    $this->dataResult = $modelList->listOfSomething($action,$infotype,array_shift ($ids));
                } else {
                    if('my' === $action and Yii::$app->request->cookies->getValue('token', false)) {
                        $access_token = Yii::$app->request->cookies->getValue('token', false);
                        $user = User::findOne(['access_token' => explode(' ',$access_token)[1]]);
                        $ids = [];
                        
                        if($user) {
                            $ids[] = $user->id;
                        }
                    }
                    $this->dataResult = $modelList->listOfSomething($action,$infotype,$ids);
                }
            }
        return $this->dataResult; 
    }
    
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