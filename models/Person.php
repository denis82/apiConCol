<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\Phonemaildata;
use yii\data\ActiveDataProvider;

class Person extends ActiveRecord
{
	const ACCESS_PUBLIC = 2;
	const EVENT_IBLOCK = 9;
	
	public $personSettings = [
				'prefConfShowMyPerson'=>'int',
				'prefConfShowMyPhoto'=>'int',
				'prefConfShowMyCompany'=>'int',
				'prefConfShowMyContacts'=>'int',
				'prefEnablePush'=>'boolean',
				'prefEnablePushNews'=>'boolean',
				'prefEnablePushEventPost'=>'boolean',
				'prefEnablePushEventAlbum'=>'boolean',
				'prefEnablePushPhotoWithMe'=>'boolean',
				'prefEnablePushEventAlarm'=>'boolean',
				'prefEnablePushNewEvent'=>'boolean'
			];
	public function getPhonemaildatas()
    {
		//var_dump($this->hasMany(Phonemaildata::className(), ['idPerson' => 'idPerson']));die;
        return $this->hasMany(Phonemaildata::className(), ['idPerson' => 'id']);
    }
	
	public function getEventSubscriptions()
    {
           return $this->hasMany(EventSubscription::className(), ['idUser' => 'id']);
    }
    
    public function getEvents()
    {
           return $this->hasMany(Event::className(), ['id' => 'idEvent'])
				->viaTable('a_eventSubscription', ['idUser' => 'id']);
    }
	
	public function getCompanys()
    {
        return $this->hasMany(Company::className(), ['company_id' => 'idCompany'])
            ->viaTable('a_companyPerson', ['idPerson' => 'id']);
    }
    
    public function getPhotos()
    {
           return $this->hasMany(Photo::className(), ['id' => 'idPhoto'])
				->viaTable('a_personPhoto', ['idPerson' => 'id']);
    }
    
    /**
     * @return string название таблицы, сопоставленной с этим ActiveRecord-классом.
     */
    public static function tableName()
    {
        return "{{%person}}" ;
    }
    
    /**
     * @return array the validation rules.
     */
	 public function rules()
	{
		return [
		
			[['middlename','surname','name'], 'string', 'length' => [2, 35]],
			[['city','country'], 'string', 'length' => [2]],
			['photo','image','extensions' => 'png, jpg'],
			[['middlename',], 'default', 'value' => ''],
			[['prefConfShowMyPerson','prefConfShowMyPhoto','prefConfShowMyCompany','prefConfShowMyContacts'],'integer'],  
			[['prefEnablePush',
				'prefEnablePushNews',
				'prefEnablePushEventPost',
				'prefEnablePushEventAlbum',
				'prefEnablePushPhotoWithMe',
				'prefEnablePushEventAlarm',
				'prefEnablePushNewEvent'
				], 'boolean']
			];
	}
	
	public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'surname' => 'Фамилия',
			'middlename' => 'Отчество',
			'city'=> 'Город',
			'country'=> 'Страна',
			'photo'=> 'Фото',
			'descr'=> 'Описание',
			'info'=> 'Инфо',
			'welcome'=> 'Приветствие',
			'blog'=> 'Блог',
			'status'=> 'Статус',
        ];
    }
    
    public function listPerson()
    {
		$list = self::findAll([
		'access' => self::ACCESS_PUBLIC]);
		return $list;
	}
	
	public function listAccess($idUser, $ids)
    {
		$users = self::findAll($ids);
		$tempArray = [];
		if ($users) {
			foreach($users as $user) {
				if(self::ACCESS_PUBLIC == $user->access and $idUser != $user->id) {
					$tempArray[] = [$idUser,$user->id,self::ACCESS_PUBLIC];
				}
			}
			return $tempArray;
		} else {
			return $tempArray;
		}
	}
	
	
	/*
	/  возращает список Экспертов для пустого action
	/
	/
	*/
	public function catalogLP()
	{
 		$listObj = Expert::find()->groupBy('idPerson')->all();
		if($listObj) {
			$list = [];
			foreach($listObj as $per) {
				$tempArray = [];
				$tempArray['id'] = $per['idPerson'];
				$tempArray['image'] = $per->persons[0]['photo'];
				$tempArray['info'] = $per->persons[0]['descr'];
				$tempArray['name'] = $per->persons[0]['name'];
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
	
	/*
	/  возращает список Событий для пустого action
	/
	/
	*/
	
	public function catalogLE()
	{
	
//==========================для битрикса========================================
// 	$listObj = Eventb::find()->where(['IBLOCK_ID' => self::EVENT_IBLOCK])->all();
// 	if($listObj) {
// 			$list = [];
// 			foreach($listObj as $per) {
// 				$tempArray = [];
// 				$tempArray['id'] = $per['ID'];
// 				$tempArray['image'] = $per['PREVIEW_PICTURE'];
// 				$tempArray['name'] = $per['NAME'];
// 				$tempArray['info'] = $per['PREVIEW_TEXT'];
//  				$tempArray['date'] = strtotime($per['DATE_CREATE']);
// 				$tempArray['hint'] = '';
// 				$tempArray['kind'] = '';
// 				$list[] = $tempArray;
// 			}
// 			return $list;
// 		} else {
// 			return $list = [];
// 		}
//============================================================================
 		$listObj = Event::find()->all();
		if($listObj) {
			$list = [];
			foreach($listObj as $per) {
				$tempArray = [];
				$tempArray['id'] = $per['id'];
				$tempArray['image'] = $per['image'];
				$tempArray['name'] = $per['name'];
				$tempArray['info'] = $per['info'];
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
	
	/*
	/  возращает список Компаний для пустого action
	/
	/
	*/
	
	public function catalogLC()
	{
 		$listObj = Company::find()->all();
		if($listObj) {
			$list = [];
			foreach($listObj as $per) {
				$tempArray = [];
				$tempArray['id'] = $per['id'];
				$tempArray['image'] = $per['logo'];
				$tempArray['info'] = $per['info'];
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
	
	public function dataLP($ids)
	{
// 		$provider = new ActiveDataProvider([
// 			'query' => Person::find()->where(['id' => 272]),
// 			
// 		]);
// 		$facets = $provider->getCount();
// 		return $facets;
		$listObj = Person::findAll($ids);
		if($listObj) {
			$list = [];
			foreach($listObj as $per) {
				$tempArray = [];
				$tempArray['id'] = $per['id'];
				$tempArray['back'] = $per['photo'];
				$tempArray['name'] = $per['name'];
				$tempArray['info'] = $per['descr'];
				$tempArray['title'] = 'Персона';
 				$tempArray['date'] = 0;
				$tempArray['withDividers'] = true;
				$tempArray['fields'] = [];
				$tempArray['kind'] = '';
				$list[] = $tempArray;
			}
			return $list;
		} else {
			return $list = [];
		}
	}
	
	public function dataLA()
	{
	
	}
	public function dataLR()
	{
	
	}
	public function dataLC($ids)
	{
		$listObj = Company::findAll($ids);
		if($listObj) {
			$list = [];
			foreach($listObj as $per) {
				$tempArray = [];
				$tempArray['id'] = $per['id'];
				$tempArray['back'] = $per['back'];
				$tempArray['name'] = $per['name'];
				$tempArray['info'] = $per['info'];
				$tempArray['title'] = 'Компания';
 				$tempArray['date'] = 0;
				$tempArray['withDividers'] = true;
				$tempArray['fields'] = [];
				$tempArray['kind'] = '';
				$list[] = $tempArray;
			}
			return $list;
		} else {
			return $list = [];
		}
	}
	public function dataLE($ids)
	{
		$listObj = Event::findAll($ids);
		if($listObj) {
			$list = [];
			foreach($listObj as $per) {
				$tempArray = [];
				$tempArray['id'] = $per['id'];
				$tempArray['back'] = $per['back'];
				$tempArray['name'] = $per['name'];
				$tempArray['info'] = $per['info'];
				$tempArray['title'] = 'Событие';
 				$tempArray['date'] = 0;
				$tempArray['withDividers'] = true;
				$tempArray['fields'] = [];
				$tempArray['kind'] = '';
				$list[] = $tempArray;
			}
			return $list;
		} else {
			return $list = [];
		}
	}
}
