<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Label extends ActiveRecord 
{
    const SCENARIO_KNOWN_PERSON = 'known';
    const SCENARIO_UNKNOWN_PERSON = 'unknown';
    
    public $dataResult = [];
    
    public function scenarios()
    {
        return [
            self::SCENARIO_KNOWN_PERSON => ['gallery_id','idPerson','left','right','top','bottom'],
            self::SCENARIO_UNKNOWN_PERSON => ['gallery_id','left','right','top','bottom'],
        ];
    }
    
    public function rules()
    {
        return [
            [['gallery_id','idPerson','left','right','top','bottom'], 'required'],
            [['left','right','top','bottom'], 'double'],
            [['gallery_id','idPerson'], 'integer'],
            [['info','name'],'string']
            ];
    }

    public static function tableName()
    {
        return "a_labels";
    }
        
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
}