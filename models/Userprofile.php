<?php

namespace app\models;

use yii\db\ActiveRecord;

class Userprofile extends ActiveRecord
{
	public function getPhonemaildata()
    {
        return $this->hasOne(Phonemaildata::className(), ['idPerson' => 'idPerson']);
    }

    
    /**
     * @return string название таблицы, сопоставленной с этим ActiveRecord-классом.
     */
    public static function tableName()
    {
        return "{{%userprofile}}" ;
    }
    
}