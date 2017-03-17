<?php

namespace app\models;

use yii\db\ActiveRecord;

class Expert extends ActiveRecord 
{
	public function getPersons()
    {
		return $this->hasMany(Person::className(), ['id' => 'idPerson']);
    }
	
	 public static function tableName()
    {
        return "{{%expert}}" ;
    }
}