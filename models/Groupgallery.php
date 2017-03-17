<?php

namespace app\models;

use yii\db\ActiveRecord;

class Groupgallery extends ActiveRecord 
{

	public static function tableName()
	{
		return "gallery_gr" ;
	}

    public function getEvents()
    {
        return $this->hasMany(Event::className(), ['gallery_gr_id' => 'gallery_gr_id']);
    }
    
     public function getImages()
    {
        return $this->hasMany(Gallery::className(), ['gallery_gr_id' => 'gallery_gr_id']);
    }

}