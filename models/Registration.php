<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;
use yii\base\Security;

class Registration extends Model
{
	public $login;
	public $password;
	public $userIp;
	public $token;
	public $surname;
	public $name;
	public $middlename;
	public $company;
	public $work;
	public $phone;
	public $errors = [];
	/**
    * @var array
    */
    public $dataResult = [];
    
	public function rules()
	{
		return [
		
			[['login','password','surname','name','company','work','phone'], 'required','message'=>'Обязательно для заполнения {attribute}.'],
			['login','email','message'=>'Не валидный email {attribute}.'],
			['userIp', 'ip'],
			['password','string','min'=>5],
			['name', 'string', 'length' => [2, 35]],
			['middlename', 'string', 'length' => [2, 35]],
			['surname', 'string', 'length' => [2, 35]],
			[['login','password','surname','name','company','work','phone'],'safe']
		];
	}
	
	public function regist()
    {
        $generate = Yii::$app->security;
        $this->attributes = Yii::$app->request->post();
        $this->token = $generate->generateRandomString();
        if ($this->validate()) {
            $rty = $this->signup();
            if($rty) {
                
                $this->dataResult['authorized'] = true;
                $this->dataResult['success'] = true;
            } else {
                $this->dataResult['authorized'] = false;
            }
            $cookies = Yii::$app->response->cookies;
            $cookies->add(new \yii\web\Cookie([
                    'name' => 'token',
                    'value' => 'Bearer '.$this->token,
                ]));
            
        } else {
            $this->dataResult['errors'] = $this->errors;
        }
        $this->dataResult['errors'] = $this->errors;
        $this->dataResult['x'] = $rty;
        return $this->dataResult;
    
	}
	 public function signup()
	{
        
		
		$person = new Person();
		//$person->id = $user->user_id;
		$person->firstname = $this->name;
		$person->middlename = $this->middlename;
		$person->surname = $this->surname;
		$person->save();
		
		$RegistDatas = ['company' => $this->company, 'work' => $this->work, 'phone' => $this->phone];
		foreach($RegistDatas as $key => $kind) {
			$phonemail = new Phonemaildata();
			if('phone' == $key) {
				$phonemail->group = 1;
			}
			$phonemail->idPerson = $person->id;
			$phonemail->kind = $key;
			$phonemail->info = $kind;
			$phonemail->save();
		}
		
		if($person->validate()) {
			if($person->save()) {
				$user = new User();
				$user->user_login = $this->login;
				$user->user_idPerson = $person->id;
				$user->user_password = md5($this->password);
				$user->user_firstname = $this->name;
				$user->user_surname = $this->surname;
				$user->user_comp = $this->company;
				$user->user_job = $this->work;
				$user->user_phone = $this->phone;
				$user->access_token = $this->token;
				if($user->validate())
				{
					$user->save();
					return true;
				} else {
                    return $user->errors;
				}
			} else {
				return false;
			}
		} else {
			$this->errors = $person->errors;
			return 1;
		}
		
	} 
	
// 	 public function signup()
// 	{
// 		$user = new User();
// 		$user->user_login = $this->login;
// 		$user->user_password = md5($this->password);
// 		$user->user_firstname = $this->name;
// 		$user->user_surname = $this->surname;
// 		$user->user_comp = $this->company;
// 		$user->user_job = $this->work;
// 		$user->user_phone = $this->phone;
// 		$user->access_token = $this->token;
// 		if($user->validate()) {
// 			if($user->save()) {
// 
// 				$person = new Person();
// 				$person->id = $user->user_id;
// 				$person->firstname = $this->name;
// 				$person->middlename = $this->middlename;
// 				$person->surname = $this->surname;
// 				$person->save();
// 				
// 				$RegistDatas = ['company' => $this->company, 'work' => $this->work, 'phone' => $this->phone];
// 				foreach($RegistDatas as $key => $kind) {
// 					$phonemail = new Phonemaildata();
// 					if('phone' == $key) {
// 						$phonemail->group = 1;
// 					}
// 					$phonemail->idPerson = $user->user_id;
// 					$phonemail->kind = $key;
// 					$phonemail->info = $kind;
// 					$phonemail->save();
// 				}
// 				return true;
// 			} else {
// 				return false;
// 			}
// 		} else {
// 			$this->errors = $user->errors;
// 		}
// 	} 
	
	
	public function changePass()
	{
		$user = new User();
		$user->user_login = $this->login;
		$user->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);//sha1($this->password);
		$user->createdAt = date('Y-m-d');
		$user->lastLoginTime = date('Y-m-d');
		$user->lastLoginIp = ip2long($this->userIp);
		$user->access_token = $this->token;
		$user->save();
	}
	
	
	public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'login' => 'Логин',
            'password' => 'Пароль',
			'userIp' => 'ip адрес',
			'token' => 'Токен',
			'surname' => 'Фамилия',
			'middlename' => 'Отчество',
			'company' => 'Компания',
			'work' => 'Должность',
			'phone' => 'Телефон',
        ];
    }
}
