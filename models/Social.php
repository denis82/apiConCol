<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;
use yii\base\Security;
use \yii\base\ErrorException;
use yii\web\IdentityInterface;

class Social extends Model
{
    public $name = 'name';
    public $login;
    public $token;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
        
            [['password'], 'required'],
            ['login','email'],
            ['password', 'valPassword'],
        ];
    }
    
    public static function socialRegistration($service) {
        
        if (!$service->getIsAuthenticated()) {
            throw new ErrorException('EAuth user should be authenticated before creating identity.');
        }

        $serviceName = $service->getServiceName(); // название соц. сети пользователя
        //$id = $service->getId(); // ид пользователя в соц. сети
        $this->name = $service->getAttribute('name');
        $matchId = SocialUser::find()->where(['social_id' => $his->login,'socialName_id' => $this->name])->one();
        try {
            if(!$matchId) {            
                $this->token = $this->generateToken();
                $person = $this->createPerson(); // создана новая персона
                $user = $this->createUser($person->id); // создан новый юзер
                $socialUser = $this->createSocialUser($user->id,$his->login); // создана привязка юзер и аккаунт соц. сети
            } 
        }
        catch (\nodge\eauth\ErrorException $e) {
            return false;
        }
        if (!$socialUser->hasErrors()) {
            $this->token = $this->generateToken();
            $user->access_token = $this->token;
            $user->save();
            $this->setCookieToken();
            return true;
        }      
        
    }
    
    public function socialLogin($service) {
        
        if (!$service->getIsAuthenticated()) {
            throw new ErrorException('EAuth user should be authenticated before creating identity.');
        }
        $idName = getIdNameSocial($this->name);
        $matchId = SocialUser::find()->where(['social_id' => $this->id,'socialName_id' => $idName])->one();
        try {
            if($matchId) {            
                $this->token = $this->generateToken();
                $modelUser = User::find()->where(['user_id' => $matchId->user_id])->one();
                $modelUser->access_token = $this->token;
                $modelUser->save();
                if (!$modelUser->hasErrors()) {
                    $this->setCookieToken();
                    return true;
                }
            }
        }
        catch (\nodge\eauth\ErrorException $e) {
            return false;
        }
    }
    
    private function createPerson()
    {
        $person = new Person();
        $person->firstname = $this->name;
        if($person->validate()) {
            $person->save();
        }
        return $person;
    }
    
    private function createUser($id = false)
    {
        $modelUser = new User();
        $modelUser->user_firstname = $this->name;
        if($id) {
            $modelUser->user_idPerson = $id;
        }
        if($modelUser->validate()){
            $modelUser->save();
        } 
        return $modelUser;
    }
    
    /**
    * Создает запись привязку пользователя к аккаунту соц. сети
    * @param integer   ид созданного пользователя 
    * @param integer   ид пользователя соц. сети 
    * @return boolean/errors 
    */
    
    public function createSocialUser($idUser,$idSocial)
    {
        $modelSocialUser = new SocialUser();
        $modelSocialUser->user_id = $idUser;
        $modelSocialUser->socialName_id = $service->getServiceName(); // название соц. сети пользователя
        $modelSocialUser->social_id = $idSocial;
        if ($modelSocialUser->validate()) {
            $modelSocialUser->save();
        }
        return $modelSocialUser;
    }
    
    
    public function generateToken()
    {
        $generate = Yii::$app->security;
        $this->token = $generate->generateRandomString();
    }
    
    public function setCookieToken()
    {
        $cookies = Yii::$app->response->cookies;
        $cookies->add(new \yii\web\Cookie([
                'name' => 'token',
                'value' => 'Bearer '.$this->token,
                ]));
    }
    
    
    public function getIdNameSocial($name)
    {
        $id = 0;
        $modelSocialName = SocialName::findOne(['socialName' => $name]);
        if($modelSocialName) {
            $id = $modelSocialName->id;
        }
        return $id;
    }
}