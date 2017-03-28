<?php

/**
 * @link http://www.con-col-lp.com/
 * @copyright Copyright (c) 2017 Picom
 */

namespace app\models;

use yii\base\Model;

/**
 *
 * @author Telegin Denis <dtelegin.spok@yandex.ru>
 */

 
 
class Fortest extends Model
{

    public $user = '2';
    public $name = 'dev\\n';

    /**
     * @return string имя таблицы
     */
    public static function tableName()
    {
        return "{{%album}}" ;
    }
}