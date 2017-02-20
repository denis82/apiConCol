<?php

namespace app\models;

//use Yii;

use yii\db\ActiveRecord;

class Eventb extends ActiveRecord 
{
	public function getEventSubscriptions()
    {
           return $this->hasMany(EventSubscription::className(), ['idEvent' => 'id']);
    }

    public function getExperts()
    {
           return $this->hasMany(Expert::className(), ['idEvent' => 'id']);
    }
    
    public function getPersons()
    {
           return $this->hasMany(Person::className(), ['id' => 'idPerson'])
				->viaTable('{{%eventSubscription}}', ['idUser' => 'id']);
    }
	
	 public static function tableName()
    {
        return "b_iblock_element" ;
    }
}