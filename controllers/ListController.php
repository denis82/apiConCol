<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Event;
use app\models\Person;
use app\models\Listing;
use app\models\Company;
use yii\base\DynamicModel;

/*	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/	В каком порядке могут приходить данные на сервер	
/!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/	ACTION 					INFOTYPE					RESULT
/	-						expert						эксперты 
/	-						event						События верхнего уровня
/	-						company						компании
/!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/	company					person						сотрудники компании
/!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/	event					expert						эксперты мероприятия
/	event					company						компании мероприятия
/	event					person						участники мероприятия
/!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/	person					event						мероприятия на которые зарегистрировался пользователь
/	person					company						компании пользователя
/!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/	my						company						мои компании
/	my						event						события на которые я зарегистрирован
/	my						person						мои знакомые персоны (те которых я добавил в свой список)
/!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ 
/ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ ВАЖНО ЗНАТЬ 
*/

class ListController extends MainapiController
{
    /*
    /	Предоставляет упорядоченный список идентификаторов элементов
    /
    /	вход		infotype - [String]  тип данных который хочу получить Person/Event/Company
    /				action - [String] элемент данные которого хочу получить. Некоторые данные не требуют
    /				ids - [Array[Int]] Идентификаторы элементов данные которых хотим получить. Указывается при наличии action (Если есть то всегда один элемент)
    /	
    /	выход		[Array]
    /					id - [Integer] идентификатор
    */

    public function init(){
        parent::init();
        $this->optionalActions = ['index'];
    }

    public function actionListindex()
    {
        $tempArray = [];
        $infotype = Yii::$app->request->post(self::INFOTYPE);
        $action = Yii::$app->request->post(self::ACTION);
        $ids = $this->simpleArray(Yii::$app->request->post(self::IDS));
        $modelList = new Listing();
        
        if(!in_array($action,['person','event','company','my'])) {  // Если action пустой то выводим только списки инфотипов  
            
            $this->tempArray = $modelList->infotypeSwitch($infotype,$ids); // модель Listing()
            
        } else {        // если action не пустой то выводим инфотипы в зависимости от action ids[]

            $this->tempArray = $modelList->actionInfotypeSwitch($infotype,$action,$ids); // модель Listing()
            
        }
        
        if (!empty($this->tempArray)) {
            $this->datas['success'] = true;
        }
        $this->checkAuth();
        $this->datas[self::DATAS] = $this->tempArray;
        return $this->datas; 
    }
}