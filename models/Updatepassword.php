<?php

namespace app\models;

use Yii;
use app\models\User;
use yii\base\Model;
use yii\base\Security;
use yii\base\ErrorException;

class Updatepassword extends Model
{
	public $token;
	public $newPassword;
	public $password;
	
	public function rules()
	{
		return [
			[['password', 'newPassword'], 'required'],
			['password', 'validatePassword'],
		];
	}
	
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
	
	
	public function getUser()
    {
		return User::findOne(['access_token' => explode(' ',$this->token)[1]]);
    }
	
	public function updatePassword()
	{
		if($this->token) {
			$user = User::find()
						->where(['access_token' => explode(' ',$this->token)[1]])
						->one();
			$user->user_password = md5($this->newPassword);
			if($user->save()) {
			return true;
			}
		} else  {
			return false;
		}
	}
}