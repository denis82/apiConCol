<?php

namespace app\controllers;

use Yii;
use \Reflection;
use yii\BaseYii;
use yii\web\Cookie;
use yii\helpers\Url;
use app\models\User;
use app\models\Userb;
use app\models\Event;
use yii\helpers\Html;
use app\models\Login;
use yii\base\Security;
use app\models\Person;
use app\myclass\Image;
use app\models\Company;
use app\models\Settings;
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

    /**
     * @var string
     */
    public $date = 'date';
    /**
     * @var int
     */
    public $stateDefault = 400;
 
            
    const VERSION = 1;
    const EVENTREGIST = 1;
    const GROUP = 'group';
    const EVENTUNREGIST = 2;
    const FIELDS = 'fields';
    const DATEINFO = 'DateInfo';
    const THEMEALBUMEVENT= 'Event';
    const USERPERSON = 'userPerson';
    const THEMEALBUMPERSON = 'Person';
    

    /**
    * Регистрация пользователя
    * @param string   $_POST['login'] логин пользователя
    * @param string   $_POST['password']  пароль пользователя
    * @param string   $_POST['surname']  Фамилия
    * @param string   $_POST['name']  Имя
    * @param string   $_POST['middlename']  Отчество
    * @param string   $_POST['company'] Компания
    * @param string   $_POST['work'] Должность
    * @param string   $_POST['phone'] Телефон
    * @return boolean/errors 
    */
    
    public function actionRegistration()
    {
        $generate = Yii::$app->security;
        $modelRegistration = new Registration();
        $modelRegistration->attributes = Yii::$app->request->post();
        $this->tempArray = $modelRegistration->regist();
        $this->datas = ArrayHelper::merge($this->datas, $this->tempArray);
        return $this->datas; 
    }

    /**
    * Обновление пароля пользователя
    * @param string   $_POST['newPassword']  пароль пользователя
    * @param string   $_POST['oldPassword']  старый пароль
    * @param string   $_POST['name']  Имя
    * @param string   $_POST['middlename']  Отчество
    * @return boolean/errors 
    */

    public function actionUpdatepassword()
    {
        $UpdatepasswordModel = new Updatepassword();
        $password = Yii::$app->request->post('oldPassword');
        $newPassword = Yii::$app->request->post('newPassword');
        $this->tempArray = $UpdatepasswordModel->updatePassword($password,$newPassword);
        $this->datas = ArrayHelper::merge($this->datas, $this->tempArray);
        $this->checkAuth();
        return $this->datas;
    }
    
    /**
     * Авторизация
     *
     * @param string $_POST['login'] логин
     * @param string $_POST['password'] пароль
     * @return boolean  - $_COOKIE['token']
     */

    public function actionLogin()
    {
        $loginModel = new Login();
        $loginModel->login = Yii::$app->request->post('login');
        $loginModel->password = Yii::$app->request->post('password');
        $loginModel->userIp = Yii::$app->request->userIP;
        $this->datas['success'] = false;
        
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
            }
        } 
        $this->datas["errors"] = $loginModel->errors;
        //$this->checkAuth();
        $this->datas[self::DATAS] = [];
        return $this->datas;
    }


    /**
     * Закрыть сессию авторизации
     * @param string - $_COOKIE['token']
     * @return boolean 
     */

    public function actionLogout()
    {
        $headers = Yii::$app->request->cookies;
        $authHeader = $headers->getValue('token', 'en');
        $loginModel = new Login();
        $result = $loginModel->logout($headers->get('token'));
        $this->datas['success'] = $result;
        
        if($result) {
            $this->datas["authorized"] = false;
        } else {
            $this->datas["authorized"] = true;
        }

        return $this->datas;
    }

    /**
     *  Проверяет зарегестрирован ли человек на событие и если да то на какие 
     *
     * @param array $_POST['ids'] идентификаторы событий 
     * @return array [
     *                  integer $state - значение состояния регистрации
     *                  Integer $eventId - идентификатор события
     *                ]
     */

    public function actionCheckeventregistration()
    {
        $idUser = Yii::$app->user->identity->getId();
        $tempIds = Yii::$app->request->post(self::IDS);
        $ids = $this->simpleArray($tempIds);
        $modelEvent = new Event();
        $this->tempArray = $modelEvent->checkeventregistration($ids);
        $this->datas = ArrayHelper::merge($this->datas, $this->tempArray);
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

//    public function actionEvents()
//    {
//        $modelEvent = new Event();
//        $this->tempArray = $modelEvent->listPersonEvent();
//        $idUser = Yii::$app->user->identity->getId();
//        $events = EventSubscription::findAll(['idUser' => $idUser]);
//        
//        if ($events) {
//            foreach ($events as $event) {
//                if (self::EVENTREGIST == $event->state) {
//                    $this->tempArray = $event->idEvent;
//                    if(!empty($this->tempArray)) {
//                        $this->datas['success'] = true;
//                    }
//                    $this->datas[self::DATAS][] = $this->tempArray;
//                }
//            }
//        } else {
//            $this->datas[self::DATAS] = [];
//        }
//        $this->datas = ArrayHelper::merge($this->datas, $this->tempArray);
//        $this->checkAuth();
//        return $this->datas;
//    }


    /*  Обновляет сведения о пользователе
    /	вход: 	token 
    /	выход:  update token 
    /	
    */
    
    /**
     *  Обновляет сведения о пользователе
     *
     * @param string  - $_COOKIE['token']
     * @param array   - $_POST['fields']  информация о персоне пользователя
     * @param file    - $imagefile - [File[image/*]][Option] - загрузить картинку фотографии пользователя (не обязательно, если этого поля нет, значит картинка остаётся прежней)[07.02.2017]
     * @param boolean[Option] -  imagefiledelete - - если true, удалить фотографию пользователя (не обязательное, по умолчанию false) [10.02.2017]
     * @return array [
     *                  userPerson - информация о персоне пользователя
     *               ]
     */

    public function actionUpdate()
    {
        
        $this->tempArray = Yii::$app->request->post(self::FIELDS);
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
            foreach($this->tempArray as $fields) {
                $phoneMail = new Phonemaildata;
                $phoneMail->attributes = $fields;
                $phoneMail->idPerson = $idUser;
                if($phoneMail->validate()) {
                    if($phoneMail->save()) {
                        $this->datas['success'] = true;
                    } else {
                        $this->datas['success'] = false;
                    }
                } else {
                    $this->datas["errors"] = $userInfo->errors;
                }	
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
        $this->datas['sdf'] = $idUser;
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

    // вывод фоток на которых я(или кто-то) отметил меня
    
    public function actionPhotoswithme()
    {
        $idUser = Yii::$app->user->identity->getId();
        if($idUser) {
            $photos = Person::find()
                            ->with('personphotos')
                            ->where(['id' => $idUser])
                            ->one();
            $arrayImg = [];
            foreach($photos->personphotos as $photo) {
                $arrayImg[] = $photo['gallery_image'];
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
        if($ids) {
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
        $settings = new Settings();
        $this->tempArray = $settings->getSettings();
        $this->checkAuth();
        $this->datas = ArrayHelper::merge($this->datas, $this->tempArray);
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
        $settings = new Settings();
        $fieldsArray = Yii::$app->request->post('params');
        $this->tempArray = $settings->setSettings($fieldsArray);
        $this->checkAuth();
        $this->datas = ArrayHelper::merge($this->datas, $this->tempArray);
        return $this->datas;
    }

    /*  Получить сведения о компании пользователя
    /	вход: 	id - идентификатор компании (если компания пользователю не принадлежит, то ошибка)
    /	
    /	выход:  [Array]
    /				<<UserPerson - (без surname, middlename) Данные компании
    /				scope - сфера деятельности 
    /	
    */

    public function actionCompany()
    {
        $modelCompany = new Company();
        $idCompany = Yii::$app->request->post('id');
        $this->tempArray = $modelCompany->getPersonCompany($idCompany);
        $this->checkAuth();
        $this->datas = ArrayHelper::merge($this->datas, $this->tempArray);
        return $this->datas;
    }

    /*  Обновляет сведения о компании пользователя
    /	вход: 		userPerson - [<<UserPerson]  - (без surname, middlename)  Данные компании
                    scope - сфера деятельности
                    imagefile - [File[image/*]][Option] - загрузить картинку значка компании (не обязательно, если этого поля нет, значит картинка остаётся прежней)
                    id - идентификатор обновляемой компании (если компания пользователю не принадлежит, то ошибка)
                    imagefiledelete - [Boolean][Option] - если true, удалить фотографию пользователя (не обязательное, по умолчанию false) 
                    create - [Boolean][Option] - если true, создать новую компанию (не обязательное, по умолчанию false) идентификатор присваивается новый 
    /	
    /	выход:  [[Array]
    /				<<UserPerson - (без surname, middlename) Данные компании
    /				scope - сфера деятельности 
    /	
    */

    public function actionUpdatecompany()
    {
        $res = [];
        $modelCompany = new Company();
        $modelCompany->idCompany = Yii::$app->request->post('id');
        $modelCompany->create = Yii::$app->request->post('create');
        $modelCompany->imagefiledelete = Yii::$app->request->post('imagefiledelete');
        $modelCompany->fields = Yii::$app->request->post('fields');
        $modelCompany->name = Yii::$app->request->post('name');
        $modelCompany->attributes = Yii::$app->request->post();
        $this->tempArray = $modelCompany->updatePersonCompany();
        $this->datas = ArrayHelper::merge($this->datas, $this->tempArray);
        $this->checkAuth();
        return $this->datas;
    }

    /*  удаляет компанию пользователя
    /	вход: 		id - идентификатор обновляемой компании (если компания пользователю не принадлежит, то ошибка)
    /	
    */

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
    
    public function actionNotificationtoken() 
    {
    
    }
    public function actionDeletenotificationtoken() 
    {
    
    }
    
    

    /*  Возвращяет подробный список сведений о персоне
    /	вход: 	$userInfo - [Integer] - id персоны
    /			$exeption - [Array] поля которые не нужны
    /	выход:  [Array] 
    /					  данные о персоне
    /	
    */

    public function person($userInfo = false,$exeption = [])
    {
        if($userInfo) {
            $userInfo = Person::find()->with('phonemaildatas')->where(['id' => $userInfo])->one();
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
            if($userInfo->photo) {
                $tempArray['image'] = Yii::getAlias('@imgHost/zBoxuersk/person/' . $userInfo->photo);
            } else {
                $tempArray['image'] = $userInfo->photo;
            }
            $tempArray['descr'] = $userInfo->descr;
            $tempArray['name'] = $userInfo->firstname;
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
}