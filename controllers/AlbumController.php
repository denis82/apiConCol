<?php

namespace app\controllers;

use Yii;
// use app\models\User;
// use app\models\Event;
// use app\models\Person;
// use app\models\Listing;
// use app\models\Company;
use app\models\Label;
use app\models\Groupgallery;
use yii\base\DynamicModel;

class AlbumController extends MainapiController
{
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
 		//$modelLabel = Label::findAll(['gallery_id' => $idImage]);
 		if(!empty($this->tempArray)) {$this->datas['success'] = true;}
 		
		$this->checkAuth();
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
    }
    
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