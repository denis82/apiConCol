<?php

namespace app\models;

use Yii;
use yii\BaseYii;
use yii\db\ActiveRecord;


class EventSubscription extends ActiveRecord
{
	const BANSTATUS = 3;
	const REGSTATUS = 1;
	const UNREGSTATUS = 2;
	
	public $success; 
	public $updateDone = true;
    
    public function getEvents()
    {
		return $this->hasMany(Event::className(), ['id' => 'idEvent']);
    }
    
    public function getPersons()
    {
		return $this->hasMany(Person::className(), ['id' => 'idUser']);
    }
    
    public static function tableName()
    {
        return "{{%eventSubscription}}" ;
    }
    
    public function rules()
	{
		return [
		
			[['idUser','idUser'], 'integer'],
			[['idUser','idUser'], 'required']
		];
	}
    
    /*
    *$state[string] - зарегистрироваться или разрегистрироваться
    *$idUser[Integer] - ид пользователя
    *$idEvents[Array[Integer]] - массив с ид мероприятий
    *return[array] - массив с ид мероприятий
    */
      public function status($idUser,$idEvents,$state = false)
    {
		$activeEvents = [];
		$existFields = [];
		$person = Person::findOne($idUser);
		$events = Event::findAll($idEvents);
		if (isset($person) and !empty($events)) { 
			$access = (self::BANSTATUS == $person->status)? false : true;  
			switch ($state) {
			
				case 'registration':  // ЕСЛИ ЗАРЕГИСТРИРОВАТЬСЯ=======================================================
					if ($access) {
						$resEvents = EventSubscription::find()->where(['idEvent' => $idEvents])->andWhere(['idUser' => $idUser])->all();
						if (isset($resEvents)) { 				// если есть записи в базе то статус нужно обновлять
							foreach($resEvents as $reaEvent) {
								if (in_array($reaEvent->idEvent, $idEvents)) {
										$existFields[] = $reaEvent->idEvent;
										$reaEvent->state = self::REGSTATUS;
										if (false === $reaEvent->update()){ 
											$this->updateDone = false;
										} else {
											$done = [];
											$done['id'] = $reaEvent->idEvent;
											$done['state'] = $reaEvent->state;
											$activeEvents[] = $done;
										}
									} 
							}
						}	
						$addTo = $this->cutArray($existFields,$idEvents);
						if ($this->updateDone and !empty($addTo)) {

							foreach($addTo as $id) {  // формирование массива 
								$idArray[] = ['idUser' => $idUser,'idEvent' => $id,'state' => self::REGSTATUS];
								$idOutArray[] = ['id' => $id,'state' => self::REGSTATUS];
							}
							$activeEvents = $idOutArray;
							$resQuery = Yii::$app->db->createCommand()->batchInsert('{{%eventSubscription}}',array('idUser','idEvent','state'),$idArray)->execute();
							
							if (0 == $resQuery) {
								$activeEvents = [];
							} 
						}	
					}	
					break;
					
				case 'unregistration':  // ЕСЛИ РАЗРЕГИСТРИРОВАТЬСЯ==================================================
					if ($access) {
						foreach($idEvents as $id) {  // формирование массива 
							$idArray[] = ['idUser' => $idUser,'idEvent' => $id,'state' => self::UNREGSTATUS];
						}
						$resEvents = EventSubscription::find()->where(['idEvent' => $idEvents])->andWhere(['idUser' => $idUser])->all();
						foreach($resEvents as $reaEvent) {
							$reaEvent->state = self::UNREGSTATUS;
							$done = [];
							$done['id'] = $reaEvent->idEvent;
							$done['state'] = $reaEvent->state;
							
							if (false === $reaEvent->update()){ break;} else {$activeEvents[] = $done;}
						}
					}
					break;
				default:
					$activeEvents = [];
			}
		} else {
			$activeEvents = [];
		}
		return $activeEvents;
    }
    
    public function cutArray($in, $source) {
		foreach($source as $key =>$item) {
			if(in_array($item,$in)) {
				unset($source[$key]);
			}
		}
		return $source;
    }
}