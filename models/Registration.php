<?php

namespace app\models;

use app\models\User;
use yii\base\Model;

class Registration extends Model
{
	public $email;
	public $password;
	public $userIp;
	public $token;
	
	public function rules()
	{
		return [
		
			[['email','password'], 'required'],
			['email','email'],
			['userIp', 'ip'],
			['email', 'unique', 'targetClass' => 'app\models\User'],
			['password','string','min'=>5] 
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
