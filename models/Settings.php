<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Person;
use yii\helpers\ArrayHelper;

class Settings extends Model 
{
    public $id;
    public $dataResult = [];

    
    /*
    /   задает настройки
    /
    /
    */
    
    public function setSettings($fieldsArray)
    {
        $this->id = Yii::$app->user->identity->getId();
        $arSettings = Yii::$app->params['settings'];
        $settings = Person::findOne($this->id);
        $settings->attributes = $fieldsArray;
        $arDiff = array_diff_assoc($fieldsArray,$settings->attributes); // если ключи входного массива кривые
        
        if ($settings->validate() and empty($arDiff)) {
            if ($settings->save()) {
                foreach($fieldsArray as $key =>$field) {
                    $type = ArrayHelper::getValue($arSettings, $key);
                    $tempArray = [];
                    $tempArray['key'] =  $key;
                    $tempArray['kind'] = ArrayHelper::getValue($arSettings, $key);
                    settype($field,$type);
                    $tempArray['value'] =  $field;
                    $this->dataResult['datas'][] = $tempArray;
                }
            }
        }
        if (!empty($this->dataResult['datas'])) {
                $this->dataResult['success'] = true;
        }
        $this->dataResult['errors'] = $settings->errors;
        
        return $this->dataResult;
    }
    
    /*
    /   получает настройки
    /
    /
    */
    
    public function getSettings()
    {
        $arSettings = Yii::$app->params['settings'];
        $this->id = Yii::$app->user->identity->getId();
        $modelPerson = Person::findOne($this->id);
        foreach($arSettings as  $key => $property) {
            $type = ArrayHelper::getValue($arSettings, $key);
            $list['key'] = $key;
            $list['kind'] = $type;
            $field = $modelPerson->$key;
            settype($field, $type);
            $list['value'] = $field;
            $this->dataResult['datas'][] = $list;
        }
        if(!empty($this->dataResult)) {
                $this->dataResult['success'] = true;
        }
        return $this->dataResult;
    }
}