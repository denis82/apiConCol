<?php

namespace app\models;

use yii\db\ActiveRecord;

class Gallery extends ActiveRecord 
{

    public static function tableName()
    {
        return "gallery" ;
    }
    
    public function getLabels()
    {
        return $this->hasMany(Label::className(), ['gallery_id' => 'gallery_id']);
    }

}