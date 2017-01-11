<?php

namespace app\controllers;

use Yii;
use yii\BaseYii;
use yii\db\Query;
use app\models\User;
use app\models\Login;
use yii\base\Security;
use app\models\UserEdit; 
use yii\rest\Controller;
use yii\db\ActiveRecord;
use app\models\Userprofile;
use app\models\Registration;
use app\models\Phonemaildata;
use app\models\EventSubscription;
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
	public $idUser = '';
	public $datas = [];
	
        
	const IDS = 'ids';
	const VERSION = 1;
	const EVENTREGIST = 3;
	const EVENTUNREGIST = 1;
	const DATAS = 'datas';
	const DATEINFO = 'DateInfo';
	const TAGKIND = 'tagKindIds';
	
	/*  Регистрация пользователя
	/	вход: пароль, логин
	/	выход: success/error
	*/
// 	public function beforeAction($action) {
// 		parent::beforeAction($action);
// 		$this->idUser = Yii::$app->user->identity->getId();		
// 		var_dump($this->idUser);
// 	}
	
	public function actionRegistration()
    {
		$generate = new Security;
		$email = Yii::$app->request->post('login');
		$password = Yii::$app->request->post('password');

		$model = new Registration();
		$model->email = $email;
		$model->password = $password;
		$model->userIp = Yii::$app->request->userIP;
		$model->token = $generate->generateRandomString();
		
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
		$loginModel = new Login();
		
		$loginModel->email = Yii::$app->request->post('login');		
		$loginModel->password = Yii::$app->request->post('oldPassword');
		$loginModel->newPassword = Yii::$app->request->post('newPassword');
		
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

		$email = Yii::$app->request->post('login');
		$password = Yii::$app->request->post('password');
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
		$headers = Yii::$app->request->headers;
		$loginModel = new Login();
		if ($headers->get('authorization')) {
			$result = $loginModel->logout($headers->get('authorization'));
		}
		$this->tempArray['success'] = $result;
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
		
	}
	
	/*  Проверяет зарегестрирован ли человек на событие и если да то на какие 
	/	вход: 	ids - [Array[Integer]] идентификаторы событий 
	/	выход:  state - [Integer]значение состояния регистрации
	/			eventId- [Integer] идентификатор события
	*/
	
	public function actionCheckeventregistration()
	{
		var_dump($this->userId); die();
		$idUser = Yii::$app->user->identity->getId();
		$tempIds = Yii::$app->request->post(self::IDS);
		$ids = $this->simpleArray($tempIds);
		if (!empty($ids)) {
			$events = EventSubscription::findAll(['idUser' => $idUser]);
			if ($events) {
				foreach ($events as $event) {
					//$this->tempArray['idUser'] = $event->idUser;
					$this->tempArray['idEvent'] = $event->idEvent;
					$this->tempArray['state'] = $event->state;
					$this->datas[self::DATAS][] = $this->tempArray;
				}
			} else {
				$this->datas[self::DATAS][] = false;
			}
		}
		return $this->datas;
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
		$idUser = Yii::$app->user->identity->getId();
		$events = EventSubscription::findAll(['idUser' => $idUser]);
		if ($events) {
			foreach ($events as $event) {
				if (self::EVENTREGIST == $event->state) {
					$this->tempArray['idEvent'] = $event->idEvent;
					$this->datas[self::DATAS][] = $this->tempArray;
				}
			}
		} else {
			$this->datas[self::DATAS][] = false;
		}
		return $this->datas;
	}
	
	/*  Обновляет сведения о пользователе
	/	вход: 	token 
	/	выход:  update token 
	/	
	*/
	
	public function actionUpdate()
	{
		$idUser = Yii::$app->user->identity->getId();
		
		$userInfo = User::findOne($idUser);
	}
	
	/*  Получить сведения о человеке
	/	вход: 	token 
	/	выход:  arrData - данные о персоне
	/	
	*/
	
	public function actionPerson()
	{


		$tempData = Yii::$app->request->post(self::IDS);
		$newData = $this->simpleArray($tempData);
		$idUser = Yii::$app->user->identity->getId();
		
		$userInfo = User::findOne($idUser);
		$tempArray['phonemaildata'] = $userInfo->phonemaildatas;
		$tempArray['id'] = $userInfo->idUser;
		$tempArray['date'] = $userInfo->bornDate;
		$tempArray['image'] = $userInfo->image;
		$tempArray['info'] = $userInfo->info;
		$tempArray['name'] = $userInfo->name;
		$tempArray['surname'] = $userInfo->surname;
		$tempArray['middlename'] = $userInfo->middlename;
		$tempArray['access'] = $userInfo->u_access;
		$this->tempArray[] = $tempArray;

		$this->datas[self::DATAS][] = $this->tempArray;
		return $this->datas;
	}
	
	
}