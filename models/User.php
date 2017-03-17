<?php

namespace app\models;

//use Yii;
use yii\web\IdentityInterface;
use app\components\MyBehavior;
use yii\db\ActiveRecord;

class User extends ActiveRecord implements IdentityInterface
{
 	public $id;
 	//public $user_idPerson;

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
        return $this->hasMany(Phonemaildata::className(), ['idUser' => 'id']);
    }
    
    public function getCompanys()
    {
        return $this->hasMany(Company::className(), ['idCompany' => 'idCompany'])
            ->viaTable('companyUser', ['idUser' => 'idUser']);
    }

	public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findOne(['access_token' => $token]);
    }
    public static function findIdentity($id)
    {
    
    }
    
    public function getId()
    {
		$this->id = $this->user_idPerson;
		return $this->id;
    }
    
    public function getAuthKey()
    {
    
    }
    
    public function validateAuthKey($authKey)
    {
    
    }
}
