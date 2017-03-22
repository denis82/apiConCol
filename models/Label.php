<?php

/**
* @link http://www.con-col-lp.com/
* @copyright Copyright (c) 2017 Picom
*/

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
*
* @author Telegin Denis <dtelegin.spok@yandex.ru>
*/

class Label extends ActiveRecord 
{
    const SCENARIO_KNOWN_PERSON = 'known';
    const SCENARIO_UNKNOWN_PERSON = 'unknown';
    /**
    * @var array
    */
    public $dataResult = [];
    
    /**
    * Возвращает список сценариев и соответствующие активные аттрибуты
    * @return array.
    */
    
    public function scenarios()
    {
        return [
            self::SCENARIO_KNOWN_PERSON => ['gallery_id','idPerson','left','right','top','bottom'],
            self::SCENARIO_UNKNOWN_PERSON => ['gallery_id','left','right','top','bottom'],
        ];
    }
    
    /**
    * @return array правила валидации
    * @see scenarios()
    */
    
    public function rules()
    {
        return [
            [['gallery_id','idPerson','left','right','top','bottom'], 'required'],
            [['left','right','top','bottom'], 'double'],
            [['gallery_id','idPerson'], 'integer'],
            [['info','name'],'string']
            ];
    }

    /**
    * @return string имя таблицы
    */
     
    public static function tableName()
    {
        return "a_labels";
    }
      
     /**
    * Возвращает информацию о метках указанной фотографии 
    * @param integer $id идентификатор альбома
    * @return array [
    *               id - [Integer]  Идентификатор метки
    *               left - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (левый край)
    *               right - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (правый край)
    *               top - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (верхний край)
    *               bottom - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (нижний край)
    *               person - [Integer] - Идентификатор персоны на которую указывает метка (0 - если это я, -1 - если метка не указывает персону)
    *               name - [String] Имя и фамилия персоны или Я
    *               info - [String] текст метки для отображения (указанный текст при создании)
    *               ]
    */
    
    public function getInfoLabel($idImage) 
    {

        $modelLabel = Label::find()->where(['gallery_id' => $idImage])->all();

        if($modelLabel) {
            foreach($modelLabel as $img){
                $tempArray = [];
                $tempArray['id'] = $img->gallery_id; 
                $tempArray['labels']['id'] = $img->id; 
                $tempArray['labels']['left'] = $img->left;
                $tempArray['labels']['right'] = $img->right;
                $tempArray['labels']['top'] = $img->top; 
                $tempArray['labels']['bottom'] = $img->bottom;
                $tempArray['labels']['person'] = $img->idPerson;
                $tempArray['labels']['name'] = $img->name;
                $tempArray['labels']['info'] = $img->info;
                $this->dataResult['datas'][] = $tempArray;
            }
        }

        if(!empty($this->dataResult)) {
            $this->dataResult['success'] = true;
            
        }
        return $this->dataResult;
    }
    
    public function getInfoLabelMe() 
    {

        $modelLabel = new Label();
        $modelLabel->attributes = Yii::$app->request->post();
        $modelLabel->idPerson = Yii::$app->user->identity->getId();
        $modelLabel->gallery_id = Yii::$app->request->post('id');
        
        if($modelLabel->validate()) {
            if($modelLabel->save()) {
                $this->dataResult['id'] = $modelLabel->id;
            }
        } else {
            $this->dataResult['errors'] = $modelLabel->errors;
        } 
        
        if(!empty($this->dataResult)) {
            $this->dataResult['success'] = true;
            
        }
        return $this->dataResult;
    }
}