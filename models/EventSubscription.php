<?php

namespace app\models;

use yii\db\ActiveRecord;


class EventSubscription extends ActiveRecord
{
	/* public function getCard()
    {
        return $this->hasMany(Card::className(), ['idLocation' => 'idLocation']);
    } */
    
    /**
     * @return string название таблицы, сопоставленной с этим ActiveRecord-классом.
     */
    public static function tableName()
    {
        return "{{%eventSubscription}}" ;
    }
    
}