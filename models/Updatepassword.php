<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;
use yii\base\Security;
use yii\base\ErrorException;

class Updatepassword extends Model
{
    public $token;
    public $newPassword;
    public $password;

    /**
    * @var array
    */
    
    public $dataResult = [];
    
    
    public function rules()
    {
        return [
            [['password', 'newPassword'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    /**
    * валидация пароля
    * @return error 
    */
    
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            
            if($user) {
                if ($user->user_password != md5($this->password)){
                    $this->addError($attribute, 'Пароль не верный.');
                }
            }
        }
    }

    /**
    * Возвращает объект пользователя
    * @return object 
    */
    
    public function getUser()
    {
        return User::findOne(['access_token' => explode(' ',$this->token)[1]]);
    }

    /**
    * Обновление пароля пользователя
    * @param string   $newPassword  пароль пользователя
    * @param string   $oldPassword  старый пароль
    * @param string   $name  Имя
    * @param string   $middlename  Отчество
    * @return boolean/errors 
    */

    public function updatePassword()
    {
        $this->dataResult['success'] = false;
        $header = Yii::$app->request->cookies;
        $authToken = $header->getValue('token', false);
        $this->token = $authToken;
        $this->password = Yii::$app->request->post('oldPassword');
        $this->newPassword = Yii::$app->request->post('newPassword');
        if($this->token) {
            $user = User::find()
                        ->where(['access_token' => explode(' ',$this->token)[1]])
                        ->one();
            $user->user_password = md5($this->newPassword);
            if ($this->validate()) {
                if($user->save()) {
                    $this->dataResult['success'] = true;
                }
            } else {
                $this->dataResult['errors'] = $this->errors;
            }
        }
        return $this->dataResult;
        
    }
}