<?php

namespace app\models;

//use Yii;

use yii\db\ActiveRecord;

class Label extends ActiveRecord 
{
	const SCENARIO_KNOWN_PERSON = 'known';
    const SCENARIO_UNKNOWN_PERSON = 'unknown';
    
    
	public function scenarios()
    {
        return [
			self::SCENARIO_KNOWN_PERSON => ['gallery_id','person','left','right','top','bottom','info'],
			self::SCENARIO_UNKNOWN_PERSON => ['gallery_id','left','right','top','bottom','info'],
        ];
    }
    
	public function rules()
	{
		return [
		
			[['gallery_id','person','left','right','top','bottom','info'], 'required'],
			[['left','right','top','bottom'], 'double'],
			[['gallery_id','person'], 'integer'],
			[['info','name'],'string']
			];
			
	}

	public static function tableName()
	{
		return "a_labels";
	}
	
	

//     public function getEvents()
//     {
//         //return $this->hasMany(Event::className(), ['gallery_gr_id' => 'gallery_gr_id']);
//     }
//     
//      public function getImages()
//     {
//         //return $this->hasMany(Gallery::className(), ['gallery_gr_id' => 'gallery_gr_id']);
//     }

}