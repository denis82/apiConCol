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
						$list = $this->PersonCompany($ids);
						break;
					case 'event':
						$list = $this->PersonEvent($ids);
						break;
					default:
						break;
				}
				break;
			case 'event':
				switch($infotype){
					case 'expert':
						$list = $this->EventExpert($ids);
						break;
					case 'company':
						$list = $this->EventCompany($ids);
						break;
					case 'person':
						$list = $this->EventPerson($ids);
						break;
					default:
						break;
				}
				break;
			case 'company':
				switch($infotype){
					case 'person':
						$list = $this->CompanyPerson($ids);
						break;
					default:
						break;
				}
			break;
			case 'my':
				switch($infotype){
					case 'person':
						$list = $this->MyPerson($ids);
						break;
					case 'company':
						$list = $ids;//$this->MyCompany($ids);
						break;
					case 'event':
						$list = $this->PersonEvent($ids);
						break;	
					default:
						break;
				}
			break;
		}
		return $list;
	}
	
}