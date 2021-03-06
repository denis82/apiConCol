<?php

namespace app\controllers;

use Yii;
use app\models\Person; 
use yii\rest\Controller;
use app\models\EventSubscription;
use yii\filters\auth\HttpBearerAuth;


class EventController extends MainapiController
{

    const REGISTRATION = 'registration';

    public $temp = [];
    
    /**
     * Зарегистрировать или снять регистрацию на мероприятие с пользователя
     *
     * @param string  - $_POST['state'] - состояние регистрация (registration, unregistration)
     * @param array   - $_POST['ids'] - мероприятия, на которые регистрируем пользователя
     * @return array [
     *                  state - [Integer] вернёт состояние регистрации пользователя 
     *                                       (смотри значения profile/сheckEventRegistration)
     *                  id - [Integer] идентификатор мероприятия
     *               ]
     */
    
    public function actionProfileregistration()
    {
        $idUser = Yii::$app->user->identity->getId();
        if($idUser) {
            $state = Yii::$app->request->post('state');
            $this->temp = $this->simpleArray(Yii::$app->request->post(self::IDS));
            if($this->temp) {  // если полученный массив ids[] валидный едем дальше
                $events = new EventSubscription;
                $this->tempArray = $events->status($idUser,$this->temp,$state); 
                if(!empty($this->tempArray)) {$this->datas['success'] = true;} 
            } else {
                $this->datas['errors'][] = 'данные массива ids не корректные';
            }
        } 
        $this->checkAuth();
        $this->datas[self::DATAS] = $this->tempArray;
        return $this->datas;
    }

    /**
     *  Зарегистрировать на мероприятия незарегистрированного пользователя пользователя
     *
     * @param array   - $_POST['fields']  информация о персоне пользователя
     * @return array [
     *                  state - [Integer] вернёт состояние регистрации пользователя 
     *                                       (смотри значения profile/сheckEventRegistration)
     *                  id - [Integer] идентификатор мероприятия
     *               ]
     */
    
    public function actionNoneprofileregistration()
    {
        $values = Yii::$app->request->post();
        $this->temp = $this->simpleArray(Yii::$app->request->post(self::IDS));
        if(!empty($this->temp)) {
            $person = new Person();
            $person->attributes = $values;
            if($person->validate()) {
                if($person->save()) {
                    $events = new EventSubscription;
                    $this->tempArray = $events->status($person->id,$this->temp,self::REGISTRATION);
                    if(!empty($this->tempArray)) {$this->datas['success'] = true;} 
                }
            }
        }
        $this->checkAuth();
        $this->datas[self::DATAS] = $this->tempArray;
        return $this->datas; 
    }
}