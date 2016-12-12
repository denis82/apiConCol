<?php

namespace app\controllers;

use Yii;
use yii\BaseYii;
use yii\db\Query;
use app\models\User;
use app\models\Signup;
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



class EventController extends MainapiController
{
	
	
	public $id = 'id';
	public $sort = 30;
	public $date = 'date';
	public $name = 'name';
	public $info = 'info';
	public $image = 'image';
	public $tagIds = 'tagIds';
	public $active = 'active';
	public $bornDate = 'bornDate';
	public $findName = 'findName';
	public $datas = [];
	public $tempArray = [];
	public $temp = [];
	
        
	const IDS = 'ids';
	const VERSION = 1;
	const DATAS = 'datas';
	
	const DATEINFO = 'DateInfo';
	const TAGKIND = 'tagKindIds';
    
    public function behaviors()
    {
	  return [
		'authenticator' => [
		  'class' => HttpBearerAuth::className(),
		],
		'access' => [
		  'class' => AccessControllAuth::className(),
		  'rules' => [
			[
			  'allow' => true,
			  'roles' => ['adminPanel'],
			],
		  ],
		],
	  
	  ];
        //$behaviors = parent::behaviors();
        //$behaviors['authenticator']['class'] = HttpBearerAuth::className();
        //$behaviors['authenticator']['only'] = ['update'];
        
        //return $behaviors;
    }
    
	public function actionProfileregistration()
	{
		$request = Yii::$app->request;
		$idArray = $request->post(self::IDS);
		$state = $request->post('state');
		$this->temp = $this->simpleArray($idArray);
		if(!empty($this->temp)) {
			if ('registration' === $state) {  // если нужно зарегистрировать пользователя
				$idArray = [];
				foreach($this->temp as $id) {
					$idArray[] = [6,$id,1];
				}
				$resQuery = Yii::$app->db->createCommand()->batchInsert('{{%eventSubscription}}',array('idUser','idEvent','state'),$idArray)->execute();
				if (0 == $resQuery ) {
					$CardStack['error'] = ['Регистрация не прошла!'];
				}
			} 
			if('unregistration' === $state) { // если нужно РАЗрегистрировать пользователя
				
			}
		}
		
		$this->datas[self::DATAS] = $CardStack;
		//$this->datas = $CardStack;
		return $this->datas;
	}
	public function actionNoneprofileregistration()
    {
		$request = Yii::$app->request;
		$idArray = $request->post(self::IDS);
		$state = $request->post('state');
		$this->temp = $this->simpleArray($idArray);
		if(!empty($this->temp)) {
			if ('registration' === $state) {  // если нужно зарегистрировать пользователя
				$idArray = [];
				foreach($this->temp as $id) {
					$idArray[] = [6,$id,1];
				}
				$resQuery = Yii::$app->db->createCommand()->batchInsert('{{%eventSubscription}}',array('idUser','idEvent','state'),$idArray)->execute();
				if (0 == $resQuery ) {
					$CardStack['error'] = ['Регистрация не прошла!'];
				}
			} 
			if('unregistration' === $state) { // если нужно РАЗрегистрировать пользователя
				
			}
		}
		
		$this->datas[self::DATAS] = $CardStack;
		//$this->datas = $CardStack;
		return $this->datas; 
	}
}