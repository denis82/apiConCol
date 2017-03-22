<?php

/**
 * @link http://www.con-col-lp.com/
 * @copyright Copyright (c) 2017 Picom
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
* @author Telegin Denis <dtelegin.spok@yandex.ru>
*/

class Event extends ActiveRecord 
{
 
    const EVENT_UNREGIST = 2;
    /**
     * @var array
     */
    public $dataResult = [];
    /**
     * @var int
     */
    public $stateDefault = 400;
        
    public function getEventSubscriptions()
    {
        return $this->hasMany(EventSubscription::className(), ['idEvent' => 'event_id']);
    }

    public function getExperts()
    {
        return $this->hasMany(Expert::className(), ['idEvent' => 'event_id']);
    }
    
    public function getPersons()
    {
        return $this->hasMany(Person::className(), ['event_id' => 'idPerson'])
                             ->viaTable('{{%eventSubscription}}', ['idUser' => 'id']);
    }
    
    public function getGalleries()
    {
        return $this->hasMany(Gallery::className(), ['gallery_gr_id' => 'gallery_gr_id']);
    }
    
    public function getEvents()
    {
        return $this->hasMany(Event::className(), ['event_id' => 'idEvent'])
                            ->viaTable('a_eventSubscription', ['idUser' => 'id']);
    }
	
    public static function tableName()
    {
        return "event" ;
    }
    
    /**
     * Метод возвращает список событий на которые пользователь зарегистрировался в виде массива
     * @return array 
     * @since 2.0.7
     */
    
    public function listPersonEvent() 
    {    
        $idUser = Yii::$app->user->identity->getId();
        $events = EventSubscription::findAll(['idUser' => $idUser]);
        if ($events) {
            foreach ($events as $event) {
                if (self::EVENT_UNREGIST == $event->state) {
                    $dataResult = $event->idEvent;
                    if(!empty($dataResult)) {
                        $this->dataResult['success'] = true;
                    }
                    $this->dataResult['datas'][] = $dataResult;
                }
            }
        } else {
            $this->dataResult['datas'] = [];
        }
        return $this->dataResult; 
    } 
    
    
    public function checkeventregistration($ids = [])
    {
        $idUser = Yii::$app->user->identity->getId();
        $tempArray = [];
        $arrEvent = [];
        if (!empty($ids)) {
            $events = EventSubscription::findAll(['idUser' => $idUser,'idEvent' => $ids]);
            if ($events) {
                foreach ($events as $event) {
                    $tempArray[] = $event->idEvent;
                    $arrEvent['id'] = $event->idEvent;
                    $arrEvent['state'] = $event->state;
                    $this->dataResult['datas'][] = $arrEvent;
                }
                $rest = array_diff($ids, $tempArray);
                foreach ($rest as $res) {
                    $arrEvent['id'] = $res;
                    $arrEvent['state'] = $this->stateDefault;
                    $this->dataResult['datas'][] = $arrEvent;
                }
                $this->dataResult['success'] = $arrEvent;
            } else {
                foreach($ids as $id) {
                    $arrEvent['id'] = $id;
                    $arrEvent['state'] = $this->stateDefault;
                    $this->dataResult['datas'][] = $arrEvent;
                }   
                
            }
            if(!empty($arrEvent)) {
                        $this->dataResult['success'] = true;
            } 
        } else {
            $this->dataResult['datas'] = [];
        }
        return $this->dataResult;
    }
}