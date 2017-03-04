<?php

namespace app\models;

use Yii;
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
    
    /*  Загружает фотку на сервер, удаляет старую(опционально) 
	/	вход: 	$path - путь по которому сохранить фотку
				$imgForDelete - название фотки которую нужно удалить
	/	выход:  $newImgName - название новой фотки либо false(если фотка с постом не пришла) 
	/	
	*/
    
    public function uploadImg($path,$imgForDelete = null)
    {
    
		$this->file = UploadedFile::getInstanceByName('imagefile');  // imagefile - получено из поста
		$newImgName = '';
		$serverName = Yii::$app->request->serverName;
		$fullPath = $_SERVER['DOCUMENT_ROOT'];
		$pathToWebSite = str_replace($serverName, "", $fullPath);
		// при переносе сайта в конфигах пути поменять !!!!!!!!!!!!!
		if ($this->file && $this->validate()) {
			
			$newImgName = Yii::$app->security->generateRandomString ( $length = 25 ) . '.' . $this->file->extension;
			if($imgForDelete) {
				$this->deleteImg($path.$imgForDelete);
			}
			
			$this->file->saveAs($pathToWebSite . $path . $newImgName);
			//$model->file->saveAs(Yii::getAlias('@app/web/uploads/userAvatars/bigSize/') . '/' . $newImgName);
// 			$image = new Image();
// 			$image->load(Yii::getAlias('@app/web/uploads/userAvatars/bigSize/') . '/' . $newImgName);   //Загружаем фото (картинку)
// 			$image->resize(256,256);     //Изменяем размер со сглаживанием.
// 			switch ($model->file->extension) {
// 				case 'jpg':
// 					$ext = IMAGETYPE_JPEG;
// 					break;
// 				case 'gif':
// 					$ext = IMAGETYPE_GIF;
// 					break;
// 				case 'png':
// 					$ext = IMAGETYPE_PNG;
// 					break;
// 			}	
// 			$image->save(Yii::getAlias('@app/web/uploads/userAvatars/smallSize/') . '/' . $newImgName ,$ext);
			return  $newImgName;
		} else {
			return false;
		}
		
    }
    
    public function deleteImg($path)
    {	
		$serverName = Yii::$app->request->serverName;
		$fullPath = $_SERVER['DOCUMENT_ROOT'];
		$pathToWebSite = str_replace($serverName, "", $fullPath);
		unlink ($pathToWebSite . $path);	
		return '';
    }
}