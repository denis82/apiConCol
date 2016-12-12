<?php




namespace app\controllers;

use Yii;
use yii\BaseYii;
use yii\db\Query;
use app\models\User;
use app\models\Registration;
use app\models\Login;
use app\models\EventSubscription;
use app\models\UserEdit; 
use yii\rest\Controller;
use app\models\Userprofile;
use app\models\Phonemaildata;
use yii\db\ActiveRecord;
use yii\filters\auth\HttpBearerAuth;
/* use app\models\Cardstack;
use app\models\CardLocation; */



class ProfileController extends MainapiController
{
	
	
	public $id = 'id';
	public $sort = 30;
	public $date = 'date';
	public $name = 'name';
	public $info = 'info';
	public $image = 'image';
	public $active = 'active';
	public $bornDate = 'bornDate';
	public $findName = 'findName';
	public $tempArray = [];
	public $datas = [];
	
        
	const IDS = 'ids';
	const VERSION = 1;
	const DATAS = 'datas';
	
	const DATEINFO = 'DateInfo';
	const TAGKIND = 'tagKindIds';
	
	/*  Регистрация пользователя
	/	вход: пароль, логин
	/	выход: success/error
	*/
	
	public function actionRegistration()
    {
		$request = Yii::$app->request;
		$email = $request->post('login');
		$password = $request->post('password');

		$model = new Registration();
		$model->email = $email;
		$model->password = $password;
		$model->userIp = Yii::$app->request->userIP;
		$model->token = generateRandomString();
		
		if ($model->validate())
		{
			$model->signup();
			$this->tempArray['success'] = true;
		} else {
			$this->tempArray['errors'] = $model->errors;

		}
		$this->tempArray['authorized'] = false;
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas; 
	}
	
	/*  Обновление пароля пользователя
	/	вход: пароль,подтверждение пароля, логин
	/	выход: success/error
	*/
	
	public function actionUpdatepassword()
	{
		$request = Yii::$app->request;
		$loginModel = new Login();
		$loginModel->email = $request->post('login');
		$loginModel->password = $request->post('oldPassword');
		$loginModel->newPassword = $request->post('newPassword');
		
		
		if ($loginModel->validate())
		{
			$this->tempArray['success'] = $loginModel->updatePassword();
		} else {
			$this->tempArray['errors'] = $loginModel->errors;
		}
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
	}
	
	
	/*  Авторизация
	/	вход: пароль, логин
	/	выход: токен 
	*/
	
	public function actionLogin()
	{//var_dump(sha1(1234567891011));die();
		$this->tempArray["errors"] = array();
		$request = Yii::$app->request;
		$email = $request->post('login');
		$password = $request->post('password');
		//$idArray = array('email' => 'doc@cum.ru', 'password' => '123456');
		$loginModel = new Login();
		$loginModel->email = $email;
		$loginModel->password = $password;
		$loginModel->userIp = Yii::$app->request->userIP;
		if ($loginModel->validate())
		{
			$accessToken = $loginModel->getToken();
			//var_dump($accessToken);die();
			if ($accessToken) {
			$cookies = Yii::$app->response->cookies;
			$cookies->add(new \yii\web\Cookie([
							'name' => 'token',
							'value' => $accessToken,
							]));
			} else {
				$this->tempArray["errors"][] = 'Ошибка соединения с базой';
			}
		} else {
			$this->tempArray['success'] = false;
		}
		$this->tempArray["authorized"] = false;
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
	}
	
	/*  Закрыть сессию авторизации
	/	вход: -
	/	выход: true
	*/
	
	public function actionLogout()
	{
	
	}
	
	/*  Проверяет зарегестрирован ли человек на событие
	/	вход: 	ids - [Array[Integer]] идентификаторы событий 
	/	выход:  state - [Integer]значение состояния регистрации
	/			eventId- [Integer] идентификатор события
	*/
	
	public function actionсheckEventRegistration()
	{
	}
	
	/*  Получить список событий на которые пользователь зарегистрировался
	/   В куках код доступа к данным пользователя, по которым сервер его определяет
	/
	/	вход: 	token 
	/	выход:  id - [Integer] идентификатор события
	/	
	*/
	
	public function actionEvents()
	{
		
	}
	
	/*  Обновляет сведения о пользователе
	/	вход: 	token 
	/	выход:  update token 
	/	
	*/
	
	public function actionUpdate()
	{
	}
	
	/*  Получить сведения о человеке
	/	вход: 	token 
	/	выход:  arrData - данные о персоне
	/	
	*/
	
	public function actionPerson()
	{
	}
}