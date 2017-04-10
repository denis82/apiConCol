<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;
use app\models\Registration;
use yii\base\Security;
use yii\base\ErrorException;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
 
class Login extends Model
{
    public $login;
    public $password;
    public $newPassword;
    public $userIp;
    public $rememberMe = true;
    public $checkToken = true;
	public $token = false;
    
    private $_user = false;
	

    /**
     * @return array the validation rules.
     */
	public function rules()
	{
		return [
		
			[['password'], 'required'],
			//['login','email'],
			['password', 'valPassword'],
		];
	}

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    
    public function valPassword($attribute, $params)
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
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
     
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }
    
    
	 public function getUser()
    {
        return User::findOne(['user_login' => $this->login]);
    }
    
     public function setPersonForUser($user)
    {
        $modelRegistration = new Registration(); 
        $modelRegistration->createPerson = true;
        return $modelRegistration->createEmptyPerson($user);
    }
    
    /**
     * 
     *
     * @return User|null
     */
    public function getToken()
    {
        if ($this->token === false) {

            $userToken = $this->getUser();
            
            if(isset($userToken)) {
                if(!$userToken->user_idPerson) {  // если персоны у юзера нет ее необходимо создать
                    $this->setPersonForUser($userToken);
                }
                $generateToken = Yii::$app->security;
                $userToken->access_token = $generateToken->generateRandomString();
                if($userToken->validate()) {
                    $userToken->save();
                    return $userToken->access_token;
                } else {
                    return false;
                    }

            }  else {
                return false;
            }
        } else {
            return false;
        }
        //var_dump($userToken->access_token);die();
        //return $userToken->access_token;
    }
    
    public function updatePassword()
	{
		if($this->token) {
			$user = User::find()
						->where(['access_token' => explode(' ',$this->token)[1]])
						->one();
			$user->password = Yii::$app->getSecurity()->generatePasswordHash($this->newPassword);
			if($user->save()) {
			return true;
			}
		} else  {
			return false;
		}
		
	}
	
	public function logout($token = false)
	{
		if($token) {
			$user = User::find()
						->where(['access_token' => explode(' ',$token)[1]])
						->one();
			if(isset($user->access_token)) {
				$user->access_token = '';
				
				if ($user->save()) { return true;} else { return false;}
				
				
				
			} else {
				$this->checkToken  = false;
				return   false;
			}
		} else {
			return false;
		}
	}
}
