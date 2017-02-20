<?php

namespace app\models;

//use Yii;
use yii\web\IdentityInterface;
use app\components\MyBehavior;
use yii\db\ActiveRecord;

class Userb extends ActiveRecord implements IdentityInterface
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
			
				[['user_login','user_password'], 'required','message'=>'Обязательно для заполнения {attribute}.'],
				['user_login','email','message'=>'Не валидный email {attribute}.'],
				['user_login', 'unique','message'=>'Пользователь с таким логином уже существует.']
			];
	}
	 public static function tableName()
    {
        return "user" ;
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

	public static function findIdentityByAccessToken($token, $type = null)
    {
		//var_dump( $token);die();
        return self::findOne(['access_token' => $token]);
    }
    public static function findIdentity($id)
    {
    
    }
    
    public function getId()
    {
		//var_dump($this->idUser);die();
		
		$this->id = $this->user_id;
		return $this->id;
    }
    
    public function getAuthKey()
    {
    
    }
    
    public function validateAuthKey($authKey)
    {
    
    }
    



}