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
    
    public $id_gall;
   /**
   * Возвращает список сценариев и соответствующие активные аттрибуты
   * @return array.
   */
   
    public function scenarios()
    {
//         return [
//             self::SCENARIO_KNOWN_PERSON => ['gallery_id','idPerson','left','right','top','bottom'],
//             self::SCENARIO_UNKNOWN_PERSON => ['gallery_id','left','right','top','bottom'],
//             'default' => ['username', 'email', 'password'],
//         ];
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_KNOWN_PERSON] = ['gallery_id','idPerson','left','right','top','bottom'];
        $scenarios[self::SCENARIO_UNKNOWN_PERSON] = ['gallery_id','left','right','top','bottom'];
        return $scenarios;
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

    public function getPersons()
    {
        return $this->hasMany(Person::className(), ['id' => 'idPerson']);
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

        $modelLabel = Gallery::find()->with(['labels.persons'])->where(['gallery_id' => $idImage])->all();
        if($modelLabel) {
            
            foreach($modelLabel as $img){
                $tempArray = [];
                //$this->id_gall = $img->gallery_id;
                
                foreach ($img->labels as $label) {
                        $tempArray ['id'] = $label['id']; 
                        $tempArray ['left'] = $label['left'];
                        $tempArray ['right'] = $label['right'];
                        $tempArray ['top'] = $label['top']; 
                        $tempArray ['bottom'] = $label['bottom'];
                        $tempArray ['person'] = $label['idPerson'];
                        $person = '';
                        foreach($label->persons as $key =>$res) {
                            $person= $res->surname .' '. $res->firstname;
                        }
                        $tempArray ['name'] = $person;
                        $tempArray ['info'] = $label['info'];
                        
                        $dataResult['labels'][] = $tempArray;    
                }
                $tempArray = [];
                $dataResult['id'] = $img->gallery_id; 
                $this->dataResult['datas'][] = $dataResult;
                $dataResult['labels'] = [];
            }
        }
        if(!empty($this->dataResult)) {
            $this->dataResult['success'] = true;
            
        }
        return $this->dataResult;
    }
    
   /**
    * Помечает пользователя на фотографии
    * @param integer $id идентификатор фотографии
    * @param string  $info  текст метки
    * @param float   $left  координаты прямоугольника в процентном соотношении от размера фотографии (левый край)
    * @param float   $right  координаты прямоугольника в процентном соотношении от размера фотографии (правый край)
    * @param float   $top  координаты прямоугольника в процентном соотношении от размера фотографии (верхний край)
    * @param float   $bottom координаты прямоугольника в процентном соотношении от размера фотографии (нижний край)
    * @return array [
    *               id - [Integer]  идентификатор созданной метки
    *               ]
    */
    
    public function setInfoLabelMe() 
    {

        $modelLabel = new Label(['scenario' => Label::SCENARIO_KNOWN_PERSON]);
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
    
    /**
    * Помечает пользователя на фотографии
    * @param integer $id идентификатор фотографии
    * @param integer $person идентификатор персоны
    * @param string  $info  текст метки
    * @param float   $left  координаты прямоугольника в процентном соотношении от размера фотографии (левый край)
    * @param float   $right  координаты прямоугольника в процентном соотношении от размера фотографии (правый край)
    * @param float   $top  координаты прямоугольника в процентном соотношении от размера фотографии (верхний край)
    * @param float   $bottom координаты прямоугольника в процентном соотношении от размера фотографии (нижний край)
    * @return array [
    *               id - [Integer]  идентификатор созданной метки
    *               ]
    */
    
    public function setInfoLabelPerson() 
    {
        $modelLabel = new Label(['scenario' => Label::SCENARIO_KNOWN_PERSON]);
        $modelLabel->attributes = Yii::$app->request->post();
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
    
    /**
    * Делает текстовую метку на фотографии
    * @param integer $id идентификатор фотографии
    * @param string  $info  текст метки
    * @param float   $left  координаты прямоугольника в процентном соотношении от размера фотографии (левый край)
    * @param float   $right  координаты прямоугольника в процентном соотношении от размера фотографии (правый край)
    * @param float   $top  координаты прямоугольника в процентном соотношении от размера фотографии (верхний край)
    * @param float   $bottom координаты прямоугольника в процентном соотношении от размера фотографии (нижний край)
    * @return array [
    *               id - [Integer]  идентификатор созданной метки
    *               ]
    */
    
    public function setInfoLabelunknown()
    {
        $modelLabel = new Label(['scenario' => Label::SCENARIO_UNKNOWN_PERSON]);
        $modelLabel->attributes = Yii::$app->request->post();
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