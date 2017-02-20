<?php

namespace app\models;

//use Yii;

use yii\db\ActiveRecord;

class Event extends ActiveRecord 
{
	public function getEventSubscriptions()
    {
           return $this->hasMany(EventSubscription::className(), ['idEvent' => 'event_id']);
    }

    public function getExperts()
    {
           return $this->hasMany(Expert::className(), ['idEvent' => 'event_id']);
    }
    
    public function getPersons()
    {
           return $this->hasMany(Person::className(), ['event_id' => 'idPerson'])
				->viaTable('{{%eventSubscription}}', ['idUser' => 'id']);
    }
	
	 public static function tableName()
    {
        //return "{{%event}}" ;
        return "event" ;
    }
}