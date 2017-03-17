<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\Phonemaildata;
use yii\data\ActiveDataProvider;

class Person extends ActiveRecord
{
	const ACCESS_PUBLIC = 2;
	const EVENT_IBLOCK = 9;
	
	
	public function getPhonemaildatas()
    {
        return $this->hasMany(Phonemaildata::className(), ['idPerson' => 'id']);
    }
	
	public function getEventSubscriptions()
    {
           return $this->hasMany(EventSubscription::className(), ['idUser' => 'id']);
    }
    
    public function getEvents()
    {
           return $this->hasMany(Event::className(), ['event_id' => 'idEvent'])
				->viaTable('a_eventSubscription', ['idUser' => 'id']);
    }
    
    public function getExperts()
    {
           return $this->hasMany(Event::className(), ['event_id' => 'idEvent'])
				->viaTable('a_expert', ['idPerson' => 'id']);
    }
	
	public function getCompanys()
    {
        return $this->hasMany(Company::className(), ['company_id' => 'company_id'])
            ->viaTable('a_companyPerson', ['idPerson' => 'id']);
    }
    
    public function getCompanyid()
    {
        return $this->hasMany(CompanyPerson::className(), ['idPerson' => 'id']);
    }
    
    public function getPersonphotos()
    {
           return $this->hasMany(Gallery::className(), ['gallery_id' => 'gallery_id'])
				->viaTable('a_labels', ['idPerson' => 'id']);
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
		
			[['middlename','surname','firstname'], 'string', 'length' => [2, 35]],
			[['city','country'], 'string', 'length' => [2]],
			['photo','image','extensions' => 'png, jpg'],
			[['middlename',], 'default', 'value' => ''],
			[['info'], 'safe'],
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
            'firstname' => 'Имя',
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
	
	/*
	/	возвращает список персон доступных для отправки визитки
	/	вход		[int] $idUser - ид пользователя отправляющего визитку
	/				[array] $ids - ид пользователей которым отправляется визитка
	/	выход		[array]  - ид пользователей кот. можно отправить визитку
	/
	*/
	
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
