<?php

namespace app\models;

use yii\base\Model;
use app\myclass\Image;
use yii\web\UploadedFile;

class UploadFile extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

   public function rules()
    {
        return [
 
            ['image', 'image', 'extensions' => 'jpg, jpeg, gif, png'],
        ];
    }
    
    public function upload($model)
    {
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
}