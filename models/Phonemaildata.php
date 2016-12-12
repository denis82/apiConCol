<?php

namespace app\models;

use yii\db\ActiveRecord;

class Phonemaildata extends ActiveRecord
{
	public function getUserprofile()
    {
        return $this->hasMany(Userprofile::className(), ['idPerson' => 'idPerson']);
    }
    /**
     * @return string название таблицы, сопоставленной с этим ActiveRecord-классом.
     */
    public static function tableName()
    {
        return "{{%phonemaildata}}" ;
    }
    
}