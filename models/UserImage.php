<?php

namespace app\models;

use yii\db\ActiveRecord;

class UserImage extends ActiveRecord
{   
    /**
     * @return string название таблицы, сопоставленной с этим ActiveRecord-классом.
     */
    public static function tableName()
    {
        return "{{%userImage}}" ;
    }
    
}