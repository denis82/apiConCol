<?php

/**
 * @link http://www.con-col-lp.com/
 * @copyright Copyright (c) 2017 Picom
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 *
 * @author Telegin Denis <dtelegin.spok@yandex.ru>
 */

class Album extends ActiveRecord 
{

    /**
     * @return string имя таблицы
     */
    public static function tableName()
    {
        return "{{%album}}" ;
    }
}