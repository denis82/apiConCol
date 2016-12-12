<?php

namespace app\models;

//use Yii;
use yii\web\IdentityInterface;
use app\components\MyBehavior;
use yii\db\ActiveRecord;

class User extends ActiveRecord implements IdentityInterface
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
    
		return $this->idUser;
    }
    
    public function getAuthKey()
    {
    
    }
    
    public function validateAuthKey($authKey)
    {
    
    }
//     public static function getId()
//     {
// 		return $this->id;
//     }
	
	
	
// 	public static function findIdentityByAccessToken($token, $type = null){
// 		return self::findOne(['acces_token' => $token]);
// 	}


}
