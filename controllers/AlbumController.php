<?php

namespace app\controllers;

use Yii;
use app\models\Label;
use app\models\Groupgallery;
use yii\base\DynamicModel;

class AlbumController extends MainapiController
{
	/*
	*	Возвращает информацию  о текущем альбоме 
	/	вход: 	id - [Integer] идентификатор альбома
	/	выход:  array [
	/				event_id - [Integer] [optional]  Идентификатор текущего эвента
	/				images - [Array] Картинки текущего альбома
	/				id - [Integer] идентификатор
	/				image - [String] картинка 
	/				sub_events_albums - [Object] [optional]
	/				id - [Integer] идентификатор альбома
	/				image - [String] картинка альбома.
	/				]
	*/			
	
	public function actionIndex()
    {
		
 		$idAlbum = Yii::$app->request->post('id');
 		$idAlbum = $this->simpleArray($idAlbum);
  		$modelGroupgallery = GroupGallery::findAll($idAlbum);
 		if($modelGroupgallery) {
			foreach ($modelGroupgallery as  $group) {
				$this->tempArray['id'] = $group->gallery_gr_id;
				$this->tempArray['event_id'] = $group->events[0]['event_id'];
				foreach ($group->images as $image) {
					$tempArray = [];
					$tempArray['id'] = $image->gallery_id;
					$tempArray['image'] = Yii::getAlias('@imgHost/zBoxuersk/gallery/'.$image->gallery_image);
					$this->tempArray['images'][] = $tempArray;
				}
				$this->tempArray['sub_events_albums'] = [];
			}
 		}
		if(!empty($this->tempArray)) {$this->datas['success'] = true;}
		$this->checkAuth();
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
	}
	
	/*
	*	Возвращает информацию о метках указанной фотографии 
	/	вход: 	id - [Integer] идентификатор альбома
	/	выход:  array [
	/				id - [Integer]  Идентификатор метки
	/				left - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (левый край)
	/				right - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (правый край)
	/				top - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (верхний край)
	/				bottom - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (нижний край)
	/				person - [Integer] - Идентификатор персоны на которую указывает метка (0 - если это я, -1 - если метка не указывает персону)
	/				name - [String] Имя и фамилия персоны или Я
	/				info - [String] текст метки для отображения (указанный текст при создании)
	/				]
	*/
	
	public function actionLabels()
    {
		$idImage = Yii::$app->request->post('id');
 		$idImage = $this->simpleArray($idImage);
 		$modelLabel = Label::findAll(['gallery_id' => $idImage]);
 		if($modelGroupgallery) {
			foreach($modelLabel as $img){
				$tempArray = [];
				$tempArray['id'] = $img->id; 
				$tempArray['left'] = $img->left;
				$tempArray['right'] = $img->right;
				$tempArray['top'] = $img->top; 
				$tempArray['bottom'] = $img->bottom;
				$tempArray['person'] = $img->person;
				$tempArray['name'] = $img->name;
				$tempArray['info'] = $img->info;
				$this->tempArray[] = $tempArray;
			}
		}	
 		if(!empty($this->tempArray)) {$this->datas['success'] = true;}
		$this->checkAuth();
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
    }
    
    /*
	*	Помечает пользователя на фотографии
	/	вход: 	id - [Integer] идентификатор фотографии
	/			info - [String] текст метки
	/			left - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (левый край)
	/			right - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (правый край)
	/			top - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (верхний край)
	/			bottom - [Float from 0.0 to 1.0] координаты прямоугольника в процентном с
	/	выход:  array [
	/				id - [Integer]  идентификатор созданной метки
	/				]
	*/
    
    public function actionLabelme()
    {
		$dataLabel = Yii::$app->request->post();
		$modelLabel = new Label(['scenario' => Label::SCENARIO_KNOWN_PERSON]);
		$modelLabel->attributes = $dataLabel;
		$modelLabel->person = Yii::$app->user->identity->getId();
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
    
    /*
	*	Помечает пользователя на фотографии
	/	вход: 	id - [Integer] идентификатор фотографии
	/			person - [Integer] идентификатор персоны
	/			info - [String] текст метки
	/			left - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (левый край)
	/			right - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (правый край)
	/			top - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (верхний край)
	/			bottom - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (нижний край)
	/	выход:  array [
	/				id - [Integer]  идентификатор созданной метки
	/				]
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
    
    /*
	*	Делает текстовую метку на фотографии
	/	вход: 	id - [Integer] идентификатор фотографии
	/			info - [String] текст метки
	/			left - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (левый край)
	/			right - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (правый край)
	/			top - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (верхний край)
	/			bottom - [Float from 0.0 to 1.0] координаты прямоугольника в процентном соотношении от размера фотографии (нижний край)
	/	выход:  array [
	/				id - [Integer]  идентификатор созданной метки
	/				]
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
    
    /*
	/	Удаляет метку с фотографии
	/	вход: 	id - [Integer] идентификатор фотографии
	/			labelIds - [Array[Integer]] идентификаторы удаляемых меток
	/	выход:  array []
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
    
    /*
	*	Удаляет все метки пользователя с фотографии (метки, указывающие этого пользователя)
	/	вход: 	id - [Integer] идентификатор фотографии
	/	выход:  array []
	*/
    
    public function actionLabelmeremove()
    {
		$idImage = Yii::$app->request->post('id');
 		$idUser= Yii::$app->user->identity->getId();
		$labels = Label::find()
							->where(['person' => $idUser])
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