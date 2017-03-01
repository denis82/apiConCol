<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $file;
    public $width;
    public $height;

   public function rules()
    {
        return [
 
            ['file', 'image', 'extensions' => 'jpg, jpeg, gif, png'],
        ];
    }
    
    public function uploadImage()
    {
    
// 		$uploadPath = Yii::getAlias($this->uploadPath);
// 		$uploadPath = rtrim($uploadPath, '/');
// 		
// 		
// 		//if($model->file) {
// 			if ($model->file && $model->validate()) {
// 				$newImgName = Yii::$app->security->generateRandomString ( $length = 25 ) . '.' . $model->file->extension;
// 				$model->file->saveAs(Yii::getAlias('@app/web/uploads/companyAvatars/bigSize/') . '/' . $newImgName);
// 				$image = new Image();
// 				$image->load(Yii::getAlias('@app/web/uploads/companyAvatars/bigSize/') . '/' . $newImgName);   //Загружаем фото (картинку)
// 				$image->resize($this->width,$this->height);     //Изменяем размер со сглаживанием.
// 				switch ($model->file->extension) {
// 					case 'jpg':
// 						$ext = IMAGETYPE_JPEG;
// 						break;
// 					case 'gif':
// 						$ext = IMAGETYPE_GIF;
// 						break;
// 					case 'png':
// 						$ext = IMAGETYPE_PNG;
// 						break;
// 				}	
// 				$image->save(Yii::getAlias('@app/web/uploads/companyAvatars/smallSize/') . '/' . $newImgName ,$ext);
// 			}
// 		//}
//         if ($this->validate()) {
//             $this->imageFile->saveAs('uploads/userAvatars/bigSize/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
//             return true;
//         } else {
//             return false;
//         }
    }
}