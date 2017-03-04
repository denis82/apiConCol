<?php

namespace app\controllers;

use Yii;
use app\models\Phonemaildata;
use app\models\Userb;
use yii\web\Controller;
use app\models\UploadForm;
use yii\web\UploadedFile;
use app\myclass\Image;
use yii\helpers\Url;
use \GD;

class TestController extends MainapiController
{
    fgdfgd
	const BANSTATUS = 3;
    
    public function actionUpdatedatagroup ()
	{
		$idUser = Yii::$app->user->identity->getId();
		$fieldsArray = Yii::$app->request->post('fields');
		if($fieldsArray and $idUser) {	
 			foreach($fieldsArray as $fields) {
				$tempArray = [];
				$tempArray[] = date('Y-m-d');
				$tempArray[] = $fields['name'];
				$tempArray[] = $fields['info'];
				$tempArray[] = $fields['kind'];
				$tempArray[] = $fields['access'];
				$tempArray[] = $fields['state'];
				$tempArray[] = $idUser;
				$tempArray[] = $fields['company'];
				$tempArray[] = $fields['group'];
				
				if(!isset($fields['group'])) {
					continue;
				}
				Phonemaildata::deleteAll(['idPerson' => $idUser,'group' => $fields['group']]);
 				$phoneMail = new Phonemaildata;
 				$phoneMail->attributes = $fields;
				$phoneMail->idPerson = $idUser;
				$phoneMail->group = $fields['group'];
				
				if($phoneMail->validate()) {
					if($phoneMail->save()) {
						$this->datas['success'] = true;
					} else {
						$this->datas['success'] = false;
						$this->datas["errors"][] = 'Phonemaildata is not save';
					}
					
				} else {
					$this->datas["errors"][] = $phoneMail->errors;
				}	
 			}
		} else {
			$this->tempArray = [];
		}
		Yii::$app->db->createCommand()->batchInsert('{{%phonemaildata}}', ['date','name','info','kind', 'access','state','idPerson','idCompany','group'], [
			['Tom',''],
			['Jane', 20],
			['Linda', 25],
		])->execute();
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
	} 
	
	 public function actionUploadw()
    {
		//Yii::setAlias('@bar', 'http://www.example.com');
		echo Url::to('@web',true);
		//echo Yii::getAlias('@web'); echo '<br>';
		//echo Yii::getAlias('@webroot'); echo '<br>';
		//echo Yii::getAlias('@runtime'); echo '<br>';
		//echo Yii::getAlias('@vendor');
		$model = new UploadForm();
		$model->file = UploadedFile::getInstanceByName('imageFile');
		
		if ($model->file && $model->validate()) {
			
			$model->file->saveAs(Yii::getAlias('@app/uploads/userAvatars/bigSize/') . '/' . $model->file->baseName . '.' . $model->file->extension);
			$image = new Image();
			$image->load(Yii::getAlias('@app/uploads/userAvatars/bigSize/') . '/' . $model->file->baseName . '.' . $model->file->extension);   //Загружаем фото (картинку)
			$image->resize(256,256);     //Изменяем размер со сглаживанием.
			switch ($model->file->extension) {
				case 'jpg':
					$ext = IMAGETYPE_JPEG;
					break;
				case 'gif':
					$ext = IMAGETYPE_GIF;
					break;
				case 'png':
					$ext = IMAGETYPE_PNG;
					break;
			}	
			$image->save(Yii::getAlias('@app/uploads/userAvatars/smallSize/') . '/' . $model->file->baseName . '.' . $model->file->extension,$ext);
		}
    }
    
    public function actionUpload()
    {
		//Yii::setAlias('@bar', 'http://www.example.com');
		$res = Userb::find()->all();
		var_dump($res);
    }
	
}