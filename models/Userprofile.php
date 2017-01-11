<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\Phonemaildata;

class Userprofile extends ActiveRecord
{
	public function getPhonemaildatas()
    {
		//var_dump($this->hasMany(Phonemaildata::className(), ['idPerson' => 'idPerson']));die;
        return $this->hasMany(Phonemaildata::className(), ['idPerson' => 'idPerson']);
    }

    
    /**
     * @return string название таблицы, сопоставленной с этим ActiveRecord-классом.
     */
    public static function tableName()
    {
        return "{{%userprofile}}" ;
    }
    
}