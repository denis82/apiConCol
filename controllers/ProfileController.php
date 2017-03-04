<?php

namespace app\controllers;

use Yii;
use yii\BaseYii;
use yii\db\Query;
use \Reflection;
use yii\web\Cookie;
use yii\helpers\Url;
use app\models\User;
use app\models\Userb;
use yii\helpers\Html;
use app\models\Login;

use yii\base\Security;
use app\models\Person;
use app\myclass\Image;
use app\models\Company;
use app\models\UserEdit;
use yii\rest\Controller;
use yii\db\ActiveRecord;
use \ReflectionProperty;
use yii\web\UploadedFile;
use app\models\ThemeAlbum;
use app\models\UploadForm;
use app\models\UploadFile;
use app\models\Userprofile;
use yii\helpers\ArrayHelper;
use app\models\Registration;
use app\models\CompanyPerson;
use app\models\Phonemaildata;
use app\models\Updatepassword;
use app\models\EventSubscription;
use yii\filters\auth\HttpBearerAuth;


class ProfileController extends MainapiController
{
	public $id = 'id';
	public $sort = 30;
	public $date = 'date';
	public $name = 'name';
	public $info = 'info';
	public $image = 'image';
	public $active = 'active';
	public $stateDefault = 400;
	public $bornDate = 'bornDate';
	public $findName = 'findName';
	       
	const VERSION = 1;
	const EVENTREGIST = 1;
	const GROUP = 'group';
	const EVENTUNREGIST = 2;
	const FIELDS = 'fields';
	const DATEINFO = 'DateInfo';
	const THEMEALBUMEVENT= 'Event';
	const USERPERSON = 'userPerson';
	const THEMEALBUMPERSON = 'Person';
	
	
	
	
	
	//public function beforeAction($action) {
// 		try {
// 		//Yii::$app->user->isGuest;
		
 
// 		return parent::beforeAction($action);
// 	}
	
	/*  Регистрация пользователя
	/	вход: пароль, логин
	/	выход: success/error
	*/
	
	
	public function actionRegistration()
    {
		$generate = Yii::$app->security;
		$values = Yii::$app->request->post();
		
		$model = new Registration();
		$model->attributes = $values;
		$model->token = $generate->generateRandomString();
		
		if ($model->validate())
		{
			if($model->signup()) {
				$this->datas['authorized'] = true;
				$this->datas['success'] = true;
			} else {
				$this->datas['authorized'] = false;
			}
			$cookies = Yii::$app->response->cookies;
			$cookies->add(new \yii\web\Cookie([
					'name' => 'token',
					'value' => 'Bearer '.$model->token,
				]));
			
		} else {
			$this->datas['errors'] = $model->errors;
		}
		$this->datas['errors'] = $model->errors;
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas; 
	}
	
	/*  Обновление пароля пользователя
	/	вход: пароль,подтверждение пароля, логин
	/	выход: success/error
	*/
	
	public function actionUpdatepassword()
	{
		$UpdatepasswordModel = new Updatepassword();
		$header = Yii::$app->request->cookies;
		$authToken = $header->getValue('token', false);
		$UpdatepasswordModel->token = $authToken;
		$UpdatepasswordModel->password = Yii::$app->request->post('oldPassword');
		$UpdatepasswordModel->newPassword = Yii::$app->request->post('newPassword');
		
		if ($UpdatepasswordModel->validate())
		{
			$this->datas['success'] = $UpdatepasswordModel->updatePassword();
		} else {
			$this->datas['errors'] = $UpdatepasswordModel->errors;
		}
		$this->checkAuth();
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
	}
	
	
	/*  Авторизация
	/	вход: пароль, логин
	/	выход: токен 
	*/
	
	public function actionLogin()
	{	
		$loginModel = new Login();
		$loginModel->login = Yii::$app->request->post('login');
		$loginModel->password = Yii::$app->request->post('password');
		$loginModel->userIp = Yii::$app->request->userIP;
		
 		if ($loginModel->validate())
 		{	
			$accessToken = $loginModel->getToken();
			
			if ($accessToken) {
				$cookies = Yii::$app->response->cookies;
				$cookies->add(new \yii\web\Cookie([
								'name' => 'token',
								'value' => 'Bearer '.$accessToken,
								]));
				$this->datas['success'] = true;
				$this->datas["authorized"] = true;
			} else {
				$this->datas["authorized"] = false;
				$this->datas['success'] = false;
				//'Ошибка соединения с базой';
			}
 		} else {
 			$this->datas["authorized"] = false;//Валидация не прошла';
 			$this->datas['success'] = false;
		}
		$this->datas["errors"] = $loginModel->errors;

		$this->datas[self::DATAS] = [];//$this->tempArray;
		return $this->datas;
	}
	
	/*  Закрыть сессию авторизации
	/	вход: -
	/	выход: true
	*/
	
	public function actionLogout()
	{
		$headers = Yii::$app->request->cookies;
		$authHeader = $headers->getValue('token', 'en');
		//$headers = Yii::$app->request->headers;
		$loginModel = new Login();
		if ($headers->get('token')) {
			$result = $loginModel->logout($headers->get('token'));
			$this->datas['success'] = $result;
			$this->datas["authorized"] = false;
		} else {
			$this->datas['success'] = false;
			$this->datas["authorized"] = false;
		}
		return $this->datas;
	}
	
	/*  Проверяет зарегестрирован ли человек на событие и если да то на какие 
	/	вход: 	ids - [Array[Integer]] идентификаторы событий 
	/	выход:  state - [Integer]значение состояния регистрации
	/			eventId- [Integer] идентификатор события
	*/
	
	public function actionCheckeventregistration()
	{
		$idUser = Yii::$app->user->identity->getId();
		$tempIds = Yii::$app->request->post(self::IDS);
		$ids = $this->simpleArray($tempIds);
		$tempArray = [];
 		if (!empty($ids)) {
 			$events = EventSubscription::findAll(['idUser' => $idUser,'idEvent' => $ids]);
 			if ($events) {
 				foreach ($events as $event) {
 					$tempArray[] = $event->idEvent;
 					$this->tempArray['id'] = $event->idEvent;
 					$this->tempArray['state'] = $event->state;
 					$this->datas[self::DATAS][] = $this->tempArray;
 				}
 				$rest = array_diff($ids, $tempArray);
 				foreach ($rest as $res) {
 					$this->tempArray['id'] = $res;
 					$this->tempArray['state'] = $this->stateDefault;
 					$this->datas[self::DATAS][] = $this->tempArray;
 				}
 				$this->datas['success'] = $this->tempArray;
 				
 			} else {
				foreach($ids as $id) {
					$this->tempArray['id'] = $id;
					$this->tempArray['state'] = $this->stateDefault;
					$this->datas[self::DATAS][] = $this->tempArray;
				}	
			if(!empty($this->tempArray)) {
					$this->datas['success'] = true;
				}
 			}
 		} else {
			$this->datas[self::DATAS] = [];
 		}
 		
 		$this->checkAuth();
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
	{//$this->datas['authorized'] = Yii::$app->user->id;
		$idUser = Yii::$app->user->identity->getId();
		if($idUser) {
			$events = EventSubscription::findAll(['idUser' => $idUser]);
			if ($events) {
				foreach ($events as $event) {
					if (self::EVENTREGIST == $event->state) {
						$this->tempArray = $event->idEvent;
						if(!empty($this->tempArray)) {
							$this->datas['success'] = true;
						}
						$this->datas[self::DATAS][] = $this->tempArray;
					}
				}
				//$this->datas["authorized"] = true;
			} else {
				$this->datas[self::DATAS] = [];
			}
		} else {
			//$this->datas[self::DATAS]["authorized"] = false;
		}
		$this->checkAuth();
		return $this->datas;
	}
	/*  Обновляет сведения о пользователе
	/	вход: 	token 
	/	выход:  update token 
	/	
	*/
	
	public function actionUpdate()
	{
		
		$this->tempArray = Yii::$app->request->post('fields');
		$idUser = Yii::$app->user->identity->getId();

		$userInfo = Person::findOne($idUser);
		$userInfo->attributes = Yii::$app->request->post();
		$model = new UploadForm();
		$newImgName = $model->uploadImg(Yii::$app->params['pathToFolderPersonInWebSite'],$userInfo->photo);
		if($newImgName) {
			$userInfo->photo = $newImgName;
		}
		
		if($userInfo->validate()) {
			if($userInfo->save()){
				$this->datas['success'] = true;
			}
		} else {
			$this->datas["errors"] = $userInfo->errors;
		}
		$phoneMail = Phonemaildata::deleteAll(['idPerson' => $idUser]);
		if(!empty($this->tempArray)) {
			$fieldsArray = $this->tempArray;
		} else {
			$fieldsArray = [];
		}
		foreach($fieldsArray as $fields) {
			$phoneMail = new Phonemaildata;
			$phoneMail->attributes = $fields;
			$phoneMail->idPerson = $idUser;
			
			if($phoneMail->validate()) {
				if($phoneMail->save()) {
					$this->datas['success'] = true;
				} else {
					$this->datas['success'] = false;
					$this->datas["errors"][] = 'Phonemaildata is not save';
				}
				
			} else {
				$this->datas["errors"] = $userInfo->errors;
			}	
		}
		$this->checkAuth();
		$this->datas[self::DATAS][] = $this->person($idUser);
		
		return $this->datas;
	}
	
	/*  Получить сведения о человеке
	/	вход: 	token 
	/	выход:  arrData - данные о персоне
	/	
	*/
	
	public function actionPerson()
	{
		$idUser = Yii::$app->user->identity->getId();
		$this->checkAuth();
		$this->tempArray = $this->person($idUser);
		if(!empty($this->tempArray)) {
			$this->datas['success'] = true;
		}
		$this->datas[self::DATAS][] = $this->person($idUser);
		return $this->datas;
	}
	
	/*  Получить альбом фотографий связанные с текущим пользователем
	/	вход: 	token 
	/	выход:  Album - изображения на которых отмечена персона
	/	
	*/
	
	public function actionPhotoswithme()
	{

		$idUser = Yii::$app->user->identity->getId();
		if($idUser) {
			$photos = Person::findOne($idUser);
			$arrayImg = [];
			foreach($photos->photos as $photo) {
				$arrayImg[] = $photo['img'];
			}
			$this->tempArray['image'] = $arrayImg;
			$this->tempArray['id'] = 0;
			$this->datas["success"] = true;
		} else {
			$this->datas["success"] = false;
		}
		$this->checkAuth();
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;

	}
	
	/*  Отправить пользователям мою визитку
	/	вход: 	ids - идентификаторы персон которым отправляем свою визитку 
	/	выход:  [Array] 
	/					id - [Integer] идентификатор персоны до которого дошла визитка
	/	
	*/
	
	public function actionSendmecard ()
	{
		$idUser = Yii::$app->user->identity->getId();
		$ids = $this->simpleArray(Yii::$app->request->post(self::IDS));
		if($idUser and $ids) {
			$person = new Person;
			$this->tempArray = $person->listAccess($idUser,$ids);
			try 
			{
				$trust = Yii::$app->db->createCommand()  
							->batchInsert('{{%trustedUsers}}', ['idPerson', 'idPersonTrust','access'],$this->tempArray)
							->execute();
			} catch (\Exception $e) {
				$trust = false;
			}
			$this->datas['success'] = true;
			if(!$trust) {
				$this->tempArray = [];
				$this->datas['success'] = false;
			}
		}
		$this->checkAuth();
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
	}
	
	/*  Обновляет сведения о пользователе только указанной группы (если group=3 стирает все записи персоны с group = 3 и вписывает новые)
	/	вход: 	fields - [Array] - новые данные  о персоне (PhoneEmaiData)
	/			group - [Integer] номер группы PhoneEmailData которую обновляем 
	/	выход:  [Array] 
	/					UserPerson - обновленные данные о персоне
	/	
	*/
	
	public function actionUpdatedatagroup ()
	{
		$idUser = Yii::$app->user->identity->getId();
		$fieldsArray = Yii::$app->request->post('fields');
		$idGroup = Yii::$app->request->post('group');
		if($fieldsArray and $idUser) {
			$phoneMail = new Phonemaildata;
			$this->tempArray = $phoneMail->updatedatagroup($idUser,array_shift($idGroup),$fieldsArray);
			if(!empty($this->tempArray)) {
				$this->datas['success'] = true;
			}
		} else {
			$this->tempArray = [];
		}
		$this->checkAuth();
		$this->datas[self::DATAS][] = $this->person($idUser);
		//$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
	}
	
	/*  Получает сведения о всех настройках пользователя
	/	
	/	выход:  [Array] 
	/					UserPerson - обновленные данные о персоне
	/
	/	!!!!!!!!!!!!!!!!!!!!!!ВАЖНО ЗНАТЬ!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	/	при добавлении новой настройки необходимо добавить в каталоге конфигов params.php в массив settings 
	/   название настройки(как в базе) и ее  предполагаемый тип
	/	!!!!!!!!!!!!!!!!!!!!!!ВАЖНО ЗНАТЬ!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	*/
	
	public function actionSettings ()
	{
		$arSettings = Yii::$app->params['settings'];
		$idUser = Yii::$app->user->identity->getId();
		$modelPerson = Person::findOne($idUser);
		foreach($arSettings as  $key => $property) {
			$type = ArrayHelper::getValue($arSettings, $key);
			$list['key'] = $key;
			$list['kind'] = $type;
			$field = $modelPerson->$key;
			settype($field, $type);
			$list['value'] = $field;
			$this->tempArray[] = $list;
		}
		if(!empty($this->tempArray)) {
				$this->datas['success'] = true;
		}
		$this->checkAuth();
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
	}
	
	
	/*  Задает изменения в настройках пользователя и возвращает их
 	/
	/	params - [Dictionary] - новые данные (в случае несоответствия типов выдавать ошибку )
	/							key - [String] идентификатор настройки
	/							value - [Boolean / Integer / Long / Double / String ] значения настройки (тип передаваемых данных зависит от типа полученного в profile/settings)
	/
	/
	/	выход:  [Array] 
	/					  обновленные настройки персоны
	/					key - [String] идентификатор настройки
	/					kind - [String] тип значения (bool / int / long / float / string)
	/					value - [Boolean / Integer / Long / Double / String ] значения настройки
	/
	/
	/	!!!!!!!!!!!!!!!!!!!!!!ВАЖНО ЗНАТЬ!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	/	при добавлении новой настройки необходимо добавить в каталоге конфигов params.php в массив settings 
	/   название настройки(как в базе) и ее  предполагаемый тип
	/	!!!!!!!!!!!!!!!!!!!!!!ВАЖНО ЗНАТЬ!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	*/
	
	public function actionSetsettings ()
	{
		$arSettings = Yii::$app->params['settings'];
		$fieldsArray = Yii::$app->request->post('params');
		$idUser = Yii::$app->user->identity->getId();
 		$settings = Person::findOne($idUser);
 		$settings->attributes = $fieldsArray;
 		$arDiff = array_diff_assoc($fieldsArray,$settings->attributes); // если ключи входного массива кривые
 		if($settings->validate() and empty($arDiff)) {
			if($settings->save()) {
				foreach($fieldsArray as $key =>$field) {
					$type = ArrayHelper::getValue($arSettings, $key);
					$tempArray = [];
					$tempArray['key'] =  $key;
					$tempArray['kind'] = ArrayHelper::getValue($arSettings, $key);
					settype($field,$type);
					$tempArray['value'] =  $field;
					$this->tempArray[] = $tempArray;
				}
			}
 		}
 		if(!empty($this->tempArray)) {
				$this->datas['success'] = true;
		}
		$this->datas['errors'] = $settings->errors;
		$this->checkAuth();
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
	}
	
	public function actionCompany()
	{
		$fieldsArray = Yii::$app->request->post('id');
		$idUser = Yii::$app->user->identity->getId();
		$exeptionFields = ['photo','surname','middlename'];
		
		$modelPerson = Person::findOne($idUser);
		$companyPerson = $modelPerson->companys;
		if($companyPerson) {
			$this->tempArray = $this->person($idUser,$exeptionFields);
			$scope = [];
			foreach($modelPerson->companys as $company) {
				$scope[] = $company['company_name']; 
			}
			$this->tempArray['scope'] = $scope;
		}
		if(!empty($this->tempArray)) {
				$this->datas['success'] = true;
		}
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
	}
	
	
	public function actionUpdatecompany()
	{
 		$idCompany = Yii::$app->request->post('id');
 		$this->tempArray = Yii::$app->request->post('fields');
		$idUser = Yii::$app->user->identity->getId();
		
		if(false == Yii::$app->request->post('create')) { // если условие выполняется то данные компании обновятся 
			$modelCompany = Company::findOne($idCompany);
		} else {  								// если условие не выполняется тогда будет создана новая компания
			$modelCompany = new Company();
			$modelCompany->company_name = Yii::$app->request->post('name');
			if($modelCompany->validate()) {
				$modelCompany->save();
			} else {
				$this->datas["errors"] = $modelCompany->errors;
			}
			$idCompany = $modelCompany->company_id;
		}	
		$modelUploadForm = new UploadForm();
			
		if(true == Yii::$app->request->post('imagefiledelete')) {	// если фотку компании нужно просто удалить
			$newImgName = $modelUploadForm->deleteImg(Yii::$app->params['pathToFolderCompanyInWebSite'].$modelCompany->company_image);
		} else { // если фотку компании нужно загрузить
			$newImgName = $modelUploadForm->uploadImg(Yii::$app->params['pathToFolderCompanyInWebSite'],$modelCompany->company_image);
		}
		$modelCompany->company_image = $newImgName;
		$modelCompany->save();  // обновление данных компании tbl company

		$phoneMail = Phonemaildata::deleteAll(['idCompany' => $idCompany]); // обновление данных компании tbl phonemaildata
		foreach($this->tempArray as $fields) {
			$phoneMail = new Phonemaildata;
			$phoneMail->attributes = $fields;
			$phoneMail->idCompany = $idCompany;
			
			if($phoneMail->validate()) {
				if($phoneMail->save()) {
					$this->datas['success'] = true;
				} else {
					$this->datas["errors"][] = 'Phonemaildata is not save';
				}
				
			} else {
				$this->datas["errors"] = $userInfo->errors;
			}	
		}
		
		if(!empty($this->tempArray)) {
				$this->datas['success'] = true;
		}
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
	}
	
	public function actionDeletecompany()
	{
		$idCompany = Yii::$app->request->post('id');
		$idUser = Yii::$app->user->identity->getId();
		$modelCompanyPerson = CompanyPerson::findOne(['idPerson' => $idUser, 'company_id' => $idCompany]);
		if($modelCompanyPerson->delete()) {
				$this->datas['success'] = true;
		}
		$this->datas[self::DATAS] = $this->tempArray;
		return $this->datas;
	}
	
	/*  Возвращяет подробный список сведений о персоне
	/	вход: 	$userInfo - [Integer] - id персоны
	/	выход:  [Array] 
	/					UserPerson - данные о персоне
	/	
	*/
	
	public function person($userInfo = false,$exeption = [])
	{
		if($userInfo) {
			$userInfo = Person::findOne($userInfo);
			if(!$userInfo)
			$tempArray['fields'] = $userInfo->phonemaildatas;
			foreach($userInfo->phonemaildatas as $keys => $fields) {
				foreach($fields as $key => $field) {
					if($this->date != $key) {
						$tempArray['fields'][$keys][$key] = $field;
					} else {
						$tempArray['fields'][$keys][$key] = strtotime($field);
					}
				}
			}
			$tempArray['id'] = $userInfo->id;
			$tempArray['city'] = $userInfo->city; 
			$tempArray['country'] = $userInfo->country; 
			$tempArray['info'] = $userInfo->info;
			$tempArray['image'] = Url::to('@web/uploads/userAvatars/smallSize/',true) . '/' .$userInfo->photo;
			$tempArray['descr'] = $userInfo->descr;
			$tempArray['name'] = $userInfo->name;
			$tempArray['firstname'] = $userInfo->firstname;
			$tempArray['surname'] = $userInfo->surname;
			$tempArray['middlename'] = $userInfo->middlename;
			$tempArray['access'] = $userInfo->status;
			if(empty($exeption)) {
				return $tempArray;
			} else {
				foreach($exeption as $exept) {
					\yii\helpers\ArrayHelper::remove($tempArray, $exept);
				}
				return $tempArray;
			}
		} else {
			return $tempArray = [];
		}
	}
	
// 	/*  Возвращяет подробный список сведений о персоне
// 	/	вход: 	$userInfo - [Integer] - id персоны
// 	/	выход:  [Array] 
// 	/					UserPerson - данные о персоне
// 	/	
// 	*/
// 	
// 	public function SetTypeSetting($field,$type)
// 	{
// 		switch($type) {
// 			case "boolean":
// 				return (bool)$field;
// 			break;
// 			case "int":
// 				return (int)$field;
// 			break;
// 			default:
// 			break;
// 		}
// 	}
}