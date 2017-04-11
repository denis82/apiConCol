<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use app\myclass\Clearstr;
use app\models\Phonemaildata;
use app\models\TrustedUsers;
use yii\base\Model;


class Catalog extends CommonLDC
{
    const NOTRUST = 0; 
    /**
    * @var array
    */
    public $dataResult = [];

    public function infotypeSwitch($infotype) 
    {    
    
        switch($infotype){
            case 'expert':
                $this->dataResult = $this->catalogLExp();  // список экспертов
                break;
            case 'event':
                $this->dataResult = $this->catalogLE(); // список событий
                break;
            case 'company':
                $this->dataResult = $this->catalogLC(); // список компаний
                break;
            case 'person':
                $this->dataResult = $this->catalogLP(); // список персон
                break;
            default:
                break;  
        }
        return $this->dataResult;
    }

    public function actionInfotypeSwitch($infotype,$action,$ids) 
    { 
        
        if (in_array($infotype,['person','event','company','expert'])) {
                
                if ($ids) {
                    
                    $this->dataResult = $this->listOfSomething($action,$infotype,array_shift ($ids)); 
                    // если ид есть то он единственный, ид не обязательный
                } else {
                    
                    if ('my' === $action and Yii::$app->request->cookies->getValue('token', false)) { // можно улучшить
                        $access_token = Yii::$app->request->cookies->getValue('token', false);
                        $user = User::findOne(['access_token' => explode(' ',$access_token)[1]]);
                        $ids = [];
                        if($user) {
                            $ids[] = $user->user_id;
                            //$r[] = $user->user_id;
                        }
                    }
                    //$r = [$action,$infotype,array_shift ($ids)];
                    $this->dataResult = $this->listOfSomething($action,$infotype,array_shift ($ids));
                    //$this->dataResult['ID'] = $r;
                }
            }
            
        return $this->dataResult;
    }
 
 
    /*
    /  возращает список Экспертов для пустого action
    /
    /
    */
    public function catalogLExp()
    {
        $listObj = Person::find()->with(['companys','companyid'])->where(['>', 'level', 0 ])->orderBy('surname')->all();
        if($listObj) {
            $list = [];
            foreach($listObj as $per) {
                $tempArray = [];
                $tempArray['id'] = $per['id'];
                $tempArray['image'] = Yii::getAlias('@imgHost/zBoxuersk/position_author/'.$per['position_author_image']);
                if(empty($per->companyid)) {
                    $tempArray['info'] = Clearstr::clear($per['descr']);
                } else {
                    $tempArray['info'] = $this->companys($per) . Clearstr::clear($per['descr']);
                }
                $tempArray['name'] = $per['surname'] . ' ' . $per['firstname'];
                $tempArray['date'] = 0;
                $tempArray['company'] = '';//$per->companys;
                //$tempArray['pos'] = $per->companyid;
                $tempArray['hint'] = '';
                $tempArray['kind'] = '';
                $list[] = $tempArray;
            }
            return $list;
        } else {
            return $list = [];
        }
    }
    
    /*
    /  возращает список Персон для пустого action
    /
    /
    */
    public function catalogLP()
    {
        $listObj = Person::find()->where(['>', 'access', 0 ])->orderBy('surname')->all();
        if($listObj) {
            $list = [];
            foreach($listObj as $per) {
                $tempArray = [];
                $tempArray['id'] = $per['id'];
                $tempArray['image'] = Yii::getAlias('@imgHost/zBoxuersk/position_author/'.$per['position_author_image']);
//                 if(empty($per->companyid)) {
//                     $tempArray['info'] = Clearstr::clear($per['descr']);
//                 } else {
//                     $tempArray['info'] = $this->companys($per) . Clearstr::clear($per['descr']);
//                 }
                $tempArray['name'] = $per['surname'] . ' ' . $per['firstname'];
                $tempArray['date'] = 0;
                $tempArray['company'] = '';//$per->companys;
                //$tempArray['pos'] = $per->companyid;
                $tempArray['hint'] = '';
                $tempArray['kind'] = '';
                $list[] = $tempArray;
            }
            return $list;
        } else {
            return $list = [1];
        }
    }
    
    /*
    /  возращает список Событий для пустого action
    /
    /
    */

    public function catalogLE()
    {
        $listObj = Event::find()->where(['>', 'event_visible', 0 ])->orderBy(['event_date' => SORT_DESC])->all();
        if($listObj) {
            $list = [];
            foreach($listObj as $per) {
                $tempArray = [];
                $tempArray['id'] = $per['event_id'];
                $tempArray['image'] = $per['event_image'];
                $tempArray['name'] = Clearstr::clear($per['event_name']);
                $tempArray['info'] = Clearstr::clear($per['event_anons']);
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

    /*
    /  возращает список Компаний для пустого action
    /
    /
    */

    public function catalogLC()
    {
        $listObj = Company::find()->where(['>', 'company_visible', 0 ])->orderBy('company_name')->all();
        if($listObj) {
            $list = [];
            foreach($listObj as $per) {
                $tempArray = [];
                $tempArray['id'] = $per['company_id'];
                $tempArray['image'] =	Yii::getAlias('@imgHost/zBoxuersk/company/' . $per['company_image']); 
                $tempArray['name'] = Clearstr::clear($per['company_name']);
                $tempArray['info'] = $per['company_anons'];
                $tempArray['date'] = 0;
                $tempArray['hint'] = '';
                $tempArray['kind'] = '';
                $list[] = $tempArray;
            }
            ArrayHelper::multisort($list, ['name'], [SORT_ASC]);
            return $list;
        } else {
            return $list = [];
        }
    }
 
    /*
    /	компании пользователя
    */
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
    
    /* 
    /	мероприятия на которые зарегистрировался пользователь
    */
    
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
    
    /*
    / 		участники мероприятия
    */
    
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

    /*
    /	 эксперты мероприятия
    */
    
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
                            $tempArray['name'] = $res['surname'];
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
    
    /*
    /	 сотрудники компании
    */
    
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
    
    /*
    / 	мои компании
    */
    
    public function MyCompany($ids)
    {
        $modelUser = User::findOne($ids); 
        $listCompany = Person::findOne($modelUser->user_idPerson); 
        if($listCompany) {
            $list = [];
            foreach($listCompany->companys as $per) {
                $tempArray = [];
                $tempArray['id'] = $per['company_id'];
                $tempArray['image'] = $per['company_logo'];
                $tempArray['info'] = $per['company_anons'];
                $tempArray['name'] = $per['company_name'];
                $tempArray['date'] = 0;
                $tempArray['hint'] = '';
                $tempArray['kind'] = 'my/company';
                $list[] = $tempArray;
            }
            return $list;
        } else {
            return $list= [];
        }
    }

    /*
    / мои знакомые персоны (те которых я добавил в свой список)
    */
    
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
    
     private function companys($personInfo) 
    {
        $comp = '';
        foreach ($personInfo->companyid as $person) {
            $info = [];
            $tempArray = [];
            foreach($personInfo->companys as $company) {
                if ($person['company_id'] == $company['company_id']) {
                    $info[0] = Clearstr::clear($company['company_name']);
                }
            }
            $info[1] = Clearstr::clear($person['position']);
            ksort($info); 
            $comp .= implode("<br>", $info)."<br>";
        }
            return $comp;	
    }
}