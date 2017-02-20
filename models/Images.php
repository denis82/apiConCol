<?php

namespace app\models;

use yii\base\ActiveRecord;
use yii\web\UploadedFile;

class UploadForm extends ActiveRecord

{
    /**
     * @var UploadedFile
     */
   public static function tableName()
    {
        return 'images';
    }

   public function rules()
    {
        return [
			[['title'], 'required'],
            ['image', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'skipOnEmpty' => $this->isNewRecord?false:true],
        ];
    }
    
    public function upload()
    {
        if ($this->validate()) {
            $this->imageFile->saveAs('uploads/userAvatars/bigSize/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
            return true;
        } else {
            return false;
        }
    }
}