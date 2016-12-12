<?php

namespace app\controllers;

use Yii;
use yii\BaseYii;
use yii\db\Query;
use app\models\EventSubscription; 
use yii\rest\Controller;
use app\models\Userprofile;
use yii\db\ActiveRecord;
use yii\filters\auth\HttpBearerAuth;
use app\components\MyBehavior;
/* use app\models\Cardstack;
use app\models\CardLocation; */



class UserpersonController extends MainapiController
{
	
	
	public $id = 'id';
	public $sort = 30;
	public $date = 'date';
	public $name = 'name';
	public $info = 'info';
	public $image = 'image';
	public $bornDate = 'bornDate';
	public $findName = 'findName';
	public $datas = [];
	public $tempArray = [];

        

	const IDS = 'ids';
	const VERSION = 1;
	const DATAS = 'datas';
	
	const DATEINFO = 'DateInfo';
	 
    
	public function actionIndex()
	{
		$request = Yii::$app->request;
		$idArray = $request->post(self::IDS);
		$temp = [];
		$tempArray = [];
		$temp = $this->simpleArray($idArray); 
		if (!empty($temp)) {
			$userInfo = Userprofile::findAll($temp);
			foreach($userInfo as $res){
				$tempArray['id'] = $res->idPerson;
				$tempArray['date'] = $res->bornDate;
				$tempArray['image'] = $res->image;
				$tempArray['info'] = $res->info;
				$tempArray['name'] = $res->name;
				$tempArray['surname'] = $res->surname;
				$tempArray['middlename'] = $res->middlename;
				//$tempArray['fields'] = $res->fields;
				//$tempArray['sort'] = $res->sort;
				$tempArray['access'] = $res->u_access;
				//$this->tempArray['status'] = $res->status;
				$this->tempArray[] = $tempArray;
			}
		}
		$this->datas[self::DATAS] = $this->tempArray;
//echo $component->beforeValidate();
//$component->prop1 = 'dfgd';
		return $this->datas;
	}
	
	public function actionEvents()
    {
		$request = Yii::$app->request;
		$idArray = $request->post(self::IDS);
		$idUser = $this->simpleArray($idArray)[0];
		if ($idUser) {
			$subscription = EventSubscription::find()
				->where(['idUser' => $idUser])
				->andWhere(['state' => 1])
				->all();
			foreach ($subscription as $sbscr) {
				$this->tempArray[] = $sbscr->idEvent;
			} 
			
		}	
		$this->datas[self::DATAS] = $this->tempArray;
		//$this->datas = $CardStack;
		return $this->datas; 
	}
	
	public function simpleArray($array)
    {
		$temp = [];
		if (null != $array and is_array($array)) {
			foreach ($array as $key => $id) {  // в цикле валидируются входные данные
				if (!is_array($id)) {
					if((int)$id) {
						$temp[] =(int)$id;
					}
				}
			}
        } else {
			$temp = [];
        }
		return $temp;
    }
}