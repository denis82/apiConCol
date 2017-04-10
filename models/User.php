<?php

namespace app\models;

//use Yii;
use yii\web\IdentityInterface;
use app\components\MyBehavior;
use yii\db\ActiveRecord;
use app\models\Photo;

class User extends ActiveRecord implements IdentityInterface
{
 	public $id;
 	public $username;
    public $password;
    public $authKey;
 	//public $user_idPerson;

	public function rules()
	{
		return [
			
				[['user_login','user_password'], 'required','message'=>'Обязательно для заполнения {attribute}.'],
				//['user_login','email','message'=>'Не валидный email {attribute}.'],
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
//         if (Yii::$app->getSession()->has('user-'.$id)) {
//             return new self(Yii::$app->getSession()->get('user-'.$id));
//         }
//         else {
            return isset(self::$users[$id]) ? new self(self::$users[$id]) : null;
       // }
    }
    
    /**
     * @param \nodge\eauth\ServiceBase $service
     * @return User
     * @throws ErrorException
     */
    public static function findByEAuth($service) {
            
        if (!$service->getIsAuthenticated()) {
            throw new ErrorException('EAuth user should be authenticated before creating identity.');
        }
        
        $id = $service->getServiceName().'-'.$service->getId();
        
        $attributes = [
            'id' => $id,
            'username' => $service->getAttribute('name'),
            'authKey' => md5($id),
            'profile' => $service->getAttributes(),
        ];
        $attributes['profile']['service'] = $service->getServiceName();
       // Yii::$app->getSession()->set('user-'.$id, $attributes);
        
        return new self($attributes);
    }
    
    public function getId()
    {
        
    $test = new Photo();
            $test->code = $this->user_idPerson;
            $test->key = 7;
            $test->save();
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
