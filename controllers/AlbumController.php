<?php

/**
* @link http://www.con-col-lp.com/
* @copyright Copyright (c) 2017 Picom
*/

namespace app\controllers;

use Yii;
use app\models\Label;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use app\models\Groupgallery;

/**
*
* @author Telegin Denis <dtelegin.spok@yandex.ru>
*/

class AlbumController extends MainapiController
{
    


    public function init(){
        parent::init();
        $this->optionalActions = ['index','labels','labelperson','labelunknown','labelremove','labelmeremove'];
    }

    /**
    * Возвращает информацию  о текущем альбоме 
    * @param integer $id идентификатор альбома.
    * @return array    [
    *               event_id - [Integer] [optional]  Идентификатор текущего эвента
    *               images - [Array] Картинки текущего альбома
    *               id - [Integer] идентификатор
    *               image - [String] картинка 
    *               sub_events_albums - [Object] [optional]
    *               id - [Integer] идентификатор альбома
    *               image - [String] картинка альбома.
    *               ]
    */

    public function actionIndex()
    {
        $idAlbum = Yii::$app->request->post('ids');
        $idAlbum = $this->simpleArray($idAlbum);
        $modelGroupgallery = GroupGallery::findAll($idAlbum);
        $tempIndex = [];
        
        if($modelGroupgallery) {

            foreach ($modelGroupgallery as  $group) {
                $tempIndex['id'] = $group->gallery_gr_id;
                $tempIndex['event_id'] = $group->events[0]['event_id'];
                
                foreach ($group->images as $image) {
                    $tempArray = [];
                    $tempArray['id'] = $image->gallery_id;
                    $tempArray['image'] = Yii::getAlias('@imgHost/zBoxuersk/gallery/'.$image->gallery_image);
                    $tempIndex['images'][] = $tempArray;
                }
                $tempIndex['sub_events_albums'] = [];
                $this->tempArray[] = $tempIndex;
            }
        }
        if(!empty($this->tempArray)) {$this->datas['success'] = true;}
        $this->checkAuth();
        $this->datas[self::DATAS] = $this->tempArray;
        return $this->datas;
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

    public function actionLabels()
    {
    $modelLabel = new Label();
    $idImage = $this->simpleArray(Yii::$app->request->post('ids'));

    $this->tempArray = $modelLabel->getInfoLabel($idImage);
    //       $idImage = Yii::$app->request->post('ids');
    //        $idImage = $this->simpleArray($idImage);
    //        $modelLabel = Label::find()->where(['gallery_id' => $idImage])->all();
    //
    //        if($modelLabel) {
    //            foreach($modelLabel as $img){
    //                $tempArray = [];
    //                $tempArray['id'] = $img->gallery_id; 
    //                $tempArray['labels']['id'] = $img->id; 
    //                $tempArray['labels']['left'] = $img->left;
    //                $tempArray['labels']['right'] = $img->right;
    //                $tempArray['labels']['top'] = $img->top; 
    //                $tempArray['labels']['bottom'] = $img->bottom;
    //                $tempArray['labels']['person'] = $img->idPerson;
    //                $tempArray['labels']['name'] = $img->name;
    //                $tempArray['labels']['info'] = $img->info;
    //                $this->tempArray[] = $tempArray;
    //            }
    //        }	
    //
    //        if(!empty($this->tempArray)) {$this->datas['success'] = true;}
    $this->checkAuth();
    $this->datas = $this->datas = ArrayHelper::merge($this->datas, $this->tempArray);
    return $this->datas;
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

    public function actionLabelme()
    {
       // $dataLabel = Yii::$app->request->post();
        $modelLabel = new Label(['scenario' => Label::SCENARIO_KNOWN_PERSON]);
        $this->tempArray = $modelLabel->getInfoLabelMe();
        //$modelLabel->attributes = $dataLabel;
        //$modelLabel->idPerson = Yii::$app->user->identity->getId();
        //$modelLabel->gallery_id = Yii::$app->request->post('id');
        
//         if($modelLabel->validate()) {
//             
//             if($modelLabel->save()) {
//                 $this->tempArray['id'] = $modelLabel->id;
//             }
//         } else {
//             $this->datas['errors'] = $modelLabel->errors;
//         } 
//         
//         if(!empty($this->tempArray)) {$this->datas['success'] = true;}
        
        $this->checkAuth();
        $this->datas = $this->datas = ArrayHelper::merge($this->datas, $this->tempArray);
        //$this->datas[self::DATAS] = $this->tempArray;
        return $this->datas;
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
    
    public function actionLabelperson()
    {
        $dataLabel = Yii::$app->request->post();
        $modelLabel = new Label(['scenario' => Label::SCENARIO_KNOWN_PERSON]);
        $modelLabel->attributes = $dataLabel;
        $modelLabel->gallery_id = Yii::$app->request->post('id');
        
        if($modelLabel->validate()) {
            
            if($modelLabel->save()) {
                $this->tempArray['id'] = $modelLabel->id;
            }
        } else {
            $this->datas['errors'] = $modelLabel->errors;
        } 
        
        if(!empty($this->tempArray)) {$this->datas['success'] = true;}
        $this->checkAuth();
        $this->datas[self::DATAS] = $this->tempArray;
        return $this->datas;
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
    
    public function actionLabelunknown()
    {
        $dataLabel = Yii::$app->request->post();
        $modelLabel = new Label(['scenario' => Label::SCENARIO_UNKNOWN_PERSON]);
        $modelLabel->attributes = $dataLabel;
        $modelLabel->gallery_id = Yii::$app->request->post('id');
        
        if($modelLabel->validate()) {
            
            if($modelLabel->save()) {
                $this->tempArray['id'] = $modelLabel->id;
            }
        } else {
            $this->datas['errors'] = $modelLabel->errors;
        } 
        
        if(!empty($this->tempArray)) {$this->datas['success'] = true;}
        $this->checkAuth();
        $this->datas[self::DATAS] = $this->tempArray;
        return $this->datas;
    }
    
    /**
    * Делает текстовую метку на фотографии
    * @param integer $id идентификатор фотографии
    * @param array   $labelIds идентификаторы удаляемых меток
    * @return array 
    */

    
    public function actionLabelremove()
    {
        $idImage = Yii::$app->request->post('id');
        $idLables = $this->simpleArray(Yii::$app->request->post('labelIds'));
        $labels = Label::find()
                            ->where(['id' => $idLables])
                            ->andWhere(['gallery_id' => $idImage])
                            ->all();
        if($labels) {					
            foreach($labels as $label) {
                if($label->delete()) {
                    $this->datas['success'] = true;
                } else {
                    break;
                }
            }
        }
        $this->checkAuth();
        $this->datas[self::DATAS] = $this->tempArray;
        return $this->datas;
    }
    
    /**
    * Удаляет все метки пользователя с фотографии (метки, указывающие этого пользователя)
    * @param integer $id идентификатор фотографии
    * @return array 
    */
    
    
    public function actionLabelmeremove()
    {
        $idImage = Yii::$app->request->post('id');
        $idUser= Yii::$app->user->identity->getId();
        $labels = Label::find()
                            ->where(['idPerson' => $idUser])
                            ->andWhere(['gallery_id' => $idImage])
                            ->all();
        if($labels) {					
            foreach($labels as $label) {
                if($label->delete()) {
                    $this->datas['success'] = true;
                } else {
                    break;
                }
            }
        }
        $this->checkAuth();
        $this->datas[self::DATAS] = $this->tempArray;
        return $this->datas;
    }
}