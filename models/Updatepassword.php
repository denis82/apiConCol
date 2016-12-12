<?php

namespace app\models;

//use Yii;
use app\models\User;
use yii\base\Model;

class Updatepassword extends Model
{
	public $email;
	public $newPassword;
	public $oldPassword;
	
	public function rules()
	{
		return [
		
			[['email','password'], 'required'],
			['email','email'],
			['password', 'validatePassword'],
		];
	}
	 public function signup()
	{
		$user = new User();
		$user->email = $this->email;
		$user->password = sha1($this->password);
		$user->createdAt = date('Y-m-d');
		$user->lastLoginTime = date('Y-m-d');
		$user->lastLoginIp = ip2long($this->userIp);
		$user->access_token = $this->token;
		//var_dump($user); die();
		$user->save();
	} 
}