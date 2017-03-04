<?php

namespace app\models;

use yii\db\ActiveRecord;

class Label extends ActiveRecord 
{
	const SCENARIO_KNOWN_PERSON = 'known';
    const SCENARIO_UNKNOWN_PERSON = 'unknown';
    
    
	public function scenarios()
    {
        return [
			self::SCENARIO_KNOWN_PERSON => ['gallery_id','idPerson','left','right','top','bottom'],
			self::SCENARIO_UNKNOWN_PERSON => ['gallery_id','left','right','top','bottom'],
        ];
    }
    
	public function rules()
	{
		return [
		
			[['gallery_id','idPerson','left','right','top','bottom'], 'required'],
			[['left','right','top','bottom'], 'double'],
			[['gallery_id','idPerson'], 'integer'],
			[['info','name'],'string']
			];
			
	}

	public static function tableName()
	{
		return "a_labels";
	}
}