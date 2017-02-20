<?php

namespace app\models;

//use Yii;

use yii\db\ActiveRecord;

class Album extends ActiveRecord 
{

    public static function tableName()
    {
        return "{{%album}}" ;
    }
}