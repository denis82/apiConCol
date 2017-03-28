<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Event;
use app\models\Person;
use app\models\Listing;
use app\models\Company;
use app\models\Catalog;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;

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
/
*/

class CatalogController extends MainapiController
{
    /*
    /Предоставляет основную информацию (которая отображается в списке) о запрашиваемых элементов (результат строится также как у list, но выводится больше данных)
    /
    /   вход        infotype - [String]  тип данных который хочу получить Person/Event/Company
    /               action - [String] элемент данные которого хочу получить. Некоторые данные не требуют
    /               ids - [Array[Int]] Идентификаторы элементов данные которых хотим получить.  Указывается при наличии action (Если есть то всегда один элемент)
    /
    /   выход       [Array]
    /                   id   - [Integer] идентификатор
    /                   name - [String] Название
    /                   info - [String] Описание
    /                   image - [String] Картинка для Preview
    /                   date - [UNIX Time] время начала (для элементов не имеющих дату значение 0 (список с элементами без дат и датами не разрешается))
    /                   hint - [String][Option] Дополнительный текст (мелким шрифтом в конце)
    /                   kind  - [String][Option] Название запрашиваемых данных (необязательный параметр используется если действие не совпадает с data/infotype согласно таблице LIST COMMANDS из команд)
    /                   style  - [String][Option] Стиль отображаемых данных для элементов, не соответствующих основному стилю (необязательный параметр)
    */

    public function init(){
        parent::init();
        $this->optionalActions = ['index'];
    }

    public function actionIndex()
    {
        $tempArray = [];
        $infotype = Yii::$app->request->post(self::INFOTYPE);
        $action = Yii::$app->request->post(self::ACTION);
        $ids = $this->simpleArray(Yii::$app->request->post(self::IDS));
        $modelList = new Catalog;
        
        if (!in_array($action,['person','event','company','my'])) {
        
            $this->tempArray = $modelList->infotypeSwitch($infotype);
            
        } else {
            
            $this->tempArray = $modelList->actionInfotypeSwitch($infotype,$action,$ids);
        }

        $this->datas['success'] = true;
        $this->checkAuth();
        $this->datas[self::DATAS] = $this->tempArray;
        return $this->datas;
    }
}
