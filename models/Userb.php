<?php

namespace app\models;

//use Yii;
use yii\web\IdentityInterface;
use app\components\MyBehavior;
use yii\db\ActiveRecord;

class Userb extends ActiveRecord 
{
// 	// логин пользователя
 	public $id;
// 	
// 	// пароль пользователя
// 	public $password;
// 	
// 	// последний ip пользователя
// 	public $lastLoginIp;
// 	
// 	// токен пользователя
// 	public $access_token;
// 	
// 	// Время последнего входа пользователя
// 	public $lastLoginTime;
// 	
// 	// дата создания пользователя
// 	public $createdAt;
// 	
// 	// дата обновления пользователя
// 	public $updatedAt;
	public function rules()
	{
		return [
			
				[['login','password'], 'required','message'=>'Обязательно для заполнения {attribute}.'],
				['login','email','message'=>'Не валидный email {attribute}.'],
				['login', 'unique','message'=>'Пользователь с таким логином уже существует.']
			];
	}
	 public static function tableName()
    {
        return "a_user" ;
    }
	
	public function getPhonemaildatas()
    {
		//var_dump($this->hasMany(Phonemaildata::className(), ['idPerson' => 'idPerson']));die;
        return $this->hasMany(Phonemaildata::className(), ['idUser' => 'id']);
    }
    
    public function getCompanys()
    {
        return $this->hasMany(Company::className(), ['idCompany' => 'idCompany'])
            ->viaTable('companyUser', ['idUser' => 'idUser']);
    }


    



}