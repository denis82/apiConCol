<?php

namespace app\models;

use Yii;
use yii\base\Model;
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
    public $email;
    public $password;
    public $newPassword;
    public $userIp;
    public $rememberMe = true;
	private $token = false;
    
    private $_user = false;
	

    /**
     * @return array the validation rules.
     */
	 public function rules()
	{
		return [
		
			[['email','password'], 'required'],
			['email','email'],
			['password', 'validatePassword'],
		];
	}

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {

        if (!$this->hasErrors()) {
            $user = User::findOne(['email' => $this->email]);
            if (!Yii::$app->getSecurity()->validatePassword($this->password, $user->password)){
                $this->addError($attribute, 'Пароль не верный.');
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

    /**
     * 
     *
     * @return User|null
     */
    public function getToken()
    {
        if ($this->token === false) {
            $userToken = User::find()
					->where(['email' => $this->email])
					->one();
        
        //$userToken->lastLoginTime
			if ($userToken->lastLoginTime !== date('Y-m-d')) {  // если пара логин пароль верна то проверяется дата последнего логина, если дата старая токен обновляется
				//$model->token = generateRandomString();
				$generateToken = new Security();
				
				$user = User::find()
						->where(['email' => $this->email])
						->one();
				if ($user) {		
					$user->lastLoginTime = date('Y-m-d');
					//var_dump(ip2long($this->userIp));
					$user->lastLoginIp = ip2long($this->userIp);
					$user->access_token = $generateToken->generateRandomString();
				
					try {
						$user->save();
						return $user->access_token;
					} catch (ErrorException $e) {
						
						return false;
					}
				} else {
					return false;
				}
			} else {
				$user = User::find()
						->where(['email' => $this->email])
						->one();
				return $user->access_token;
			}
        } else {
			return false;
        }
		//var_dump($userToken->lastLoginTime);die();
        return $userToken->access_token;
    }
    
    public function updatePassword()
	{
		$user = User::find()
					->where(['email' => $this->email])
					->one();
		$user->password = Yii::$app->getSecurity()->generatePasswordHash($this->newPassword);
		if($user->save()) {
		return true;
		}
		
	}
	
	public function logout($token)
	{
		$user = User::find()
					->where(['access_token' => explode(' ',$token)[1]])
					->one();
		$user->access_token = '';
		if($user->save()) {
			return true;
		} else {
			return false;
		}
	}
}
