<?php

namespace app\models;

//use Yii;

use yii\db\ActiveRecord;

class Gallery extends ActiveRecord 
{

    public static function tableName()
    {
        return "gallery" ;
    }

}