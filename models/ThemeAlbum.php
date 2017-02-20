<?php

namespace app\models;

//use Yii;

use yii\db\ActiveRecord;

class ThemeAlbum extends ActiveRecord 
{


/**
     * @return string название таблицы, сопоставленной с этим ActiveRecord-классом.
     */
    public static function tableName()
    {
        return "{{%themeAlbum}}" ;
    }
	
// 	public function getEventSubscriptions()
//     {
//            return $this->hasMany(EventSubscription::className(), ['idEvent' => 'id']);
//     }

    

}