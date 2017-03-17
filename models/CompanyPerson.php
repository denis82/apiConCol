<?php

namespace app\models;

//use Yii;

use yii\db\ActiveRecord;

class CompanyPerson extends ActiveRecord 
{
	 /**
     * @return string название таблицы, сопоставленной с этим ActiveRecord-классом.
     */
     
    public static function tableName()
    {
        return "{{%companyPerson}}" ;
    }
    
	public function getPersons()
    {
        return $this->hasMany(Person::className(), ['id' => 'idPerson']);
    }


}