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



class ApiController extends Controller
{
	
	
	public $id = 'id';
	public $sort = 30;
	public $date = 'date';
	public $name = 'name';
	public $info = 'info';
	public $image = 'image';
	public $tagIds = 'tagIds';
	public $active = 'active';
	public $cardIds = 'cardIds';
	public $latitude = 'latitude';
	public $bornDate = 'bornDate';
	public $findName = 'findName';
	public $topCardId = 'topCardId';
	public $longitude = 'longitude';
	public $tagKindIds = 'tagkindIds';
	public $locationIds = 'locationIds';
	public $idCardStack = 'cardStackIds';
	public $datas = [];
	
        
	const TAG = 1;
	const TAGK = 2;
	const IDS = 'ids';
	const VERSION = 1;
	const RADIUS = 0.5;
	const TAGS = 'tags';
	const DATAS = 'datas';
	const RAD = 'distance';
	const LAT = 'latitude';
	const LON = 'longitude';
	
	const DATEINFO = 'DateInfo';
	const TAGKIND = 'tagKindIds';
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::className();
        $behaviors['authenticator']['except'] = ['index'];
        return $behaviors;
    }
    
	public function actionIndex()
	{
		return  "asdf";
	}
	public function actionRegistration()
    {
		$request = Yii::$app->request;
		$email = $request->post('login');
		
		$password = $request->post('password');
		$CardStack = [];

		$model = new Signup();
		$model->email = $email;
		$model->password = $password;
		
		/* if($model->password and $model->email) {
			$CardStack['success'] = true;
		} else {
			$CardStack['success'] = false;
		} */
		//var_dump($model->email.'=>'.$model->password);die();
		if ($model->validate())
		{
			$model->signup();
			$CardStack['success'] = true;
		} else {
			$CardStack['success'] = false;
		}
		$CardStack['errors'] = array();
		$CardStack['authorized'] = false;
		$this->datas[self::DATAS] = $CardStack;
		//$this->datas = $CardStack;
		return $this->datas; 
	}
	
	public function actionAuthorization()
    {
		$request = Yii::$app->request;
		$email = $request->post('login');
		$password = $request->post('password');
		$CardStack = [];
		//$idArray = array('email' => 'doc@cum.ru', 'password' => '123456');
		$auth_model = new Login();
		$auth_model->email = $email;
		$auth_model->password = $password;
		if ($auth_model->validate())
		{
			$CardStack['success'] = true;
		} else {
			$CardStack['success'] = false;
		}
		$CardStack["errors"] = array();
		$CardStack["authorized"] = false;
		$this->datas[self::DATAS] = $CardStack;
		return $this->datas; 
	}
	
    public function actionUserperson()
    {   
		//$request = Yii::$app->request;
		//$idArray = $request->post(self::IDS);
        $CardStack = [];
        //$arrayId = [];
        //$temp = [];
		//$temp = $this->simpleArray($idArray);
		$userPhones = new Phonemaildata();
		$userInfo = new Userprofile();
		$resStackInfo = $userInfo::find()->where("`idPerson` = 4")->all();
		$resStackPhones = $userPhones::find()->where("`idPerson` = 4")->all();
		$tempcart = [];
		foreach ($resStackInfo as $res) {
			foreach ($resStackPhones as $result) {
				$tempaArr = [];
				$tempaArr['date']= $result['date'];
				$tempaArr['tip']= $result['tip'];
				$tempaArr['value']= $result['value'];
				$tempaArr['kind']= $result['kind'];
				$tempaArr['access']= $result['access'];
				$tempaArr['state']= $result['state'];
				$tempaArr['sort']= $result['sort'];
				$tempcart['fields'][] = $tempaArr;
			}
			$tempcart['idPerson'] = '$id';
			$tempcart[$this->date] = strtotime($res[$this->bornDate]);
			$tempcart[$this->image] = $res[$this->image];
			$tempcart[$this->info] = $res[$this->info];
			$tempcart[$this->name]  = $res[$this->name];
			$tempcart['surname']  = $res['surname'];
			$tempcart['middlename']  = $res['middlename'];			
			
			$tempcart['sort'] = $this->sort;
			$tempcart['access'] =$res['u_access'];
			
	
		}
		if(!empty($tempcart)) {
				$CardStack[] = $tempcart;
				}
		

	
	
	
		/* 
		

	
		
		
		
			
			 foreach($resStackInfo as  $val) {
					//if(isset($val) and null != $val) {
					//$c = $val;
					$tempcart[] = $val; 
						// foreach($val as  $ResVal) {
							// if(isset($ResVal) and null != $ResVal) {
								// var_dump('val');
							// }
						// }
					//}
				}
				//echo "\n";

		//} */
		//var_dump($CardStack);die;
		 $this->datas = $CardStack;
        return $this->datas;  
    }
    
    public function actionPhonemaildata()
    {   
		$temp = (new \yii\db\Query())
			->select(['cardStackIds'])
			->from('l_cardStack')
			->orderBy('RAND()')
			->limit(200)
			->all();
        $CardStack = [];
		if (!empty($temp)) {
			foreach ($temp as $idArray) {
				foreach ($idArray as $id) {
					$CardStack[] = $id;
				} 		
			} 		
		} else {				
			$CardStack = [];
		}	
		$this->datas[self::DATAS] = $CardStack;
        //var_dump($CardStack);die;
        return $this->datas; 
    }
	
	
    
	public function actionCheckeventregistration() // Profile Check Event Registration
    {   
		$request = Yii::$app->request;
		$idArray = $request->post(self::IDS);
		$CardStack = [];
		$temp = [];
		$temp = $this->simpleArray($idArray); 
		if (!empty($temp)) {
			$customers = EventSubscription::find()
				->where(['idUser' => 16])
				->all();
			
			foreach ($customers as $id) {
				$idArray = []; 
				if(in_array ( $id->idEvent , $temp )) {
					$idArray['eventId'] = $id->idEvent;
					$idArray['state'] = $id->state;
					$CardStack[] = $idArray;
				}	 		
			}
		} else {				
			 $CardStack = [];
		}	
		$this->datas[self::DATAS] = $CardStack;
        //var_dump($CardStack);die;
        return $this->datas;  
    }
	
    public function actionProfileupdate()
    {   
		//$request = Yii::$app->request;
		//$idArray = $request->post(self::TAGS);
		$idArray = array('id' => 4, 'name' => 'Петров');
        $CardStack = [];
        $aliasTable = '';
        $oldIds = '';
        $ChangeStringForQuery = '';
        $count = 1;
		if ($idArray['id']) {
			$user = Userprofile::findOne($idArray['id']);
			$user->bornDate = $idArray['date'];
			$user->name = $idArray['name'];
			$user->image = $idArray['image'];
			$user->info = $idArray['info'];
			$user->surname = $idArray['surname'];
			$user->middlename = $idArray['middlename'];
			$user->save();
		}
		if ($idArray['idphone']) {
			$user = Phonemaildata::findOne($idArray['id']);
			$user->bornDate = $idArray['date'];
			$user->name = $idArray['name'];
			$user->image = $idArray['image'];
			$user->info = $idArray['info'];
			$user->surname = $idArray['surname'];
			$user->middlename = $idArray['middlename'];
			$user->save();
		}
        /* if($this->checkArray($idArray)) {
			foreach($idArray as $ids) { 
				if ( $aliasTable == '') {
					$aliasTable = 'alias';
				}
				if (isset($idArray[$count])) {
				$ChangeStringForQuery .= ' INNER JOIN 
					(SELECT cardIds FROM l_cardTag 
						WHERE tagIds IN ('.implode(',',$idArray[$count]) .')) as `'.$aliasTable.$count.'` ON '.$aliasTable.$oldIds.'.cardIds = '.$aliasTable.$count.'.cardIds';
				$oldIds = $count;
				}		
				$aliasTable = 'alias';					
				$count++;
			}		
			$query ="SELECT cardStackIds FROM 
					(SELECT alias.cardIds FROM l_cardTag as `alias`	
					".$ChangeStringForQuery."
					WHERE alias.tagIds IN (".implode(',',$idArray[array_keys($idArray)[0]]) .")) as `res` 
					INNER JOIN   `l_card` as `c` ON res.cardIds = c.cardIds 
					GROUP BY cardStackIds";
			$resPost = Yii::$app->db->createCommand($query)->queryAll();
			foreach ($resPost as $res) {
				foreach($res as $r) {
					$CardStack[] = $r;
				}
			}	
		} else {
			$CardStack = [];
        }
		$this->datas[self::DATAS] = $CardStack; */
        //var_dump($query);die;
        return $this->datas; 
    }
    /*
    *
    * $id = [optional]
    */

    public function actionEventsubscription()
    {
		$request = Yii::$app->request;
		$state = $request->post('state');
		$idArray = $request->post(self::IDS);
		//$idArray = array(array(1),array(2),array(3));//$request->post(self::IDS);
		$CardStack = [];
		$temp = [];
		$temp = $this->simpleArray($idArray);
		if(!empty($temp)) {
			if ($state === 'registration') {
			$idArray = [];
			 foreach($temp as $id) {
				 $idArray[] = [6,$id,1];
			}
			//var_dump($idArray);die;
			$resQuery = Yii::$app->db->createCommand()->batchInsert('{{%eventSubscription}}',array('idUser','idEvent','state'),$idArray)->execute();
			if (0 == $resQuery ) {
				$CardStack['error'] = ['Регистрация не прошла!'];
			}
			//var_dump($resQuery);die();
			/* $event = new EventSubscription();
			$event->idUser = '19';
			$event->idEvent = '4';
			$event->save(); */
			
			
			}
			
			// if ($state === 'unregistration') {
				
			// }
		}
        //var_dump($resQuery);die;
		$this->datas[self::DATAS] = $CardStack;
        return $this->datas; 
    }
    
    public function actionEvents ()
    {
		$request = Yii::$app->request;
		$idArray = $request->post(self::IDS);
		$CardStack = [];
		$temp = [];
		$temp = $this->simpleArray($idArray);
		if(!empty($temp)) {
			$query = 'SELECT date, locationIds as `id` ,latitude,longitude, active
						FROM  `l_location` as `c`
						WHERE locationIds IN ('.implode(' , ', $temp).')';
			$resQuery = Yii::$app->db->createCommand($query)->queryAll();
			foreach ($temp as $id) {
			
				$tempcart = [];
				foreach ($resQuery as $res) {
					if ($id == $res['id']) {
						$tempcart['id'] = $res['id'];
						$tempcart[$this->date] = strtotime($res[$this->date]);
						($res[$this->active]) ? $tempcart[$this->active] = true : $tempcart[$this->active] = false;
						$tempcart[$this->latitude] = $res[$this->latitude]; 
						$tempcart[$this->longitude] = $res[$this->longitude];
						$tempcart['sort'] = $this->sort;	
					}	
				}
				$CardStack[] = $tempcart;
			}
		}	else {
			$CardStack = [];
        }	
		//var_dump($resQuery);die;   
		$this->datas[self::DATAS] = $CardStack;
        return $this->datas; 
    }
    
    /*
    *
    * $id = [optional]
    */
/*     public function actionUserperson()
    {
		$request = Yii::$app->request;
		$idArray = $request->post(self::IDS);
		$tagArray = $request->post(self::TAGKIND);
		$stringForQuery = '';
		$CardStack = [];
		$tagTemp = [];
		$idsTemp = [];
		$tagTemp = $this->simpleArray($tagArray);
		$idsTemp = $this->simpleArray($idArray);
		if (empty($tagTemp) and  empty($idsTemp)) {
			$stringForQuery = '';
		}
		if (empty($tagTemp) and  !empty($idsTemp)) {
			$stringForQuery = 'WHERE tagIds IN ('.implode(' , ', $idsTemp).')';
		}
		if (!empty($tagTemp) and  empty($idsTemp)) {
			$stringForQuery = 'WHERE tagKindIds IN ('.implode(' , ', $tagTemp).')';
		}
		if (!empty($tagTemp) and  !empty($idsTemp)) {
			$stringForQuery = 'WHERE tagIds IN ('.implode(' , ', $idsTemp).') AND tagKindIds IN ('.implode(' , ', $tagTemp).')';
		}
		$query = 'SELECT tagIds as `id`, date, findName, name, tagkindIds as `tagkindId`
					FROM  `l_tag`'.$stringForQuery; 
		$resQuery = Yii::$app->db->createCommand($query)->queryAll();
		foreach ($resQuery as $id) {
		
			$tempcart = [];
			foreach ($resQuery as $res) {
				if ($id['id'] == $res['id']) {
					$tempcart['id'] = $res['id'];
					$tempcart[$this->date] = strtotime($res[$this->date]);
					$tempcart['tagkindId'] = $res['tagkindId'];
					$tempcart[$this->name] = $res[$this->name]; 
					$tempcart[$this->findName] = $res[$this->findName];
					//$tempcart['sort'] = $this->sort;	
                                        
				}	
			}
			$CardStack[] = $tempcart; 
		}
		//var_dump(strtotime("0000-00-00"));die;   
		$this->datas[self::DATAS] = $CardStack;
        return $this->datas; 
    } */
    
    /*
    *
    * $id = [optional]
    */
    public function actionTagkind()
    {
		$request = Yii::$app->request;
		$idArray = $request->post(self::IDS);
		$CardStack = [];
		$temp = [];
		$temp = $this->simpleArray($idArray);
		if(!empty($temp)) {
			$endQuery = 'WHERE  tk.tagKindIds IN ('.implode(' , ', $temp).')';
		}	else {
			$endQuery = '';
        }
        
		$query = 'SELECT tk.date, tk.name, tk.tagkindIds as `tagkindIds` FROM `l_tagKind` as `tk`
					
					'.$endQuery.''; //INNER JOIN `l_tag` as `t`  ON t.tagKindIds =tk.tagKindIds 
		$resQuery = Yii::$app->db->createCommand($query)->queryAll();
		if(empty($temp)) {
			foreach ($resQuery as $res) {
				$temp[] = $res[$this->tagKindIds];
			}
		}
		
		foreach($temp as $id) {  // формируется вывод из результата запроса
			$tempcart = [];
			foreach ($resQuery as $res) {
				if ( $res[$this->tagKindIds] == $id) {
					if(isset($tempcart[$this->tagIds])) {
						if (!in_array($res[$this->tagIds],$tempcart[$this->tagIds])) {
							//$tempcart[$this->tagIds][] = 	$res[$this->tagIds];
						}
					} else {
						//$tempcart[$this->tagIds][] = 	$res[$this->tagIds];
					}
					$tempcart[$this->date] = strtotime($res[$this->date]);
					$tempcart[$this->name]  = $res[$this->name];
					$tempcart['sort'] = $this->sort;
					$tempcart['id'] = $id;
				}
			}
			$CardStack[] = $tempcart;
		//var_dump($temp);die;   
		}	
		//var_dump($tempcart);die;   
		$this->datas[self::DATAS] = $CardStack;
        return $this->datas; 
    }
    
    public function actionLocationsearch()
    {  
        $request = Yii::$app->request;
        $latitude = $request->post(self::LAT);
        $longitude = $request->post(self::LON);
        $radius = $request->post(self::RAD);
        $CardStack = [];
        false == (int)$radius ? $radius = self::RADIUS : $radius = ((int)$radius)/1000;
        if($radius and floatval($longitude) and floatval($latitude)) {
					
			$query ='SELECT locationIds, ( 6371 * acos( cos( radians('.$latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( latitude ) ) ) ) AS distance FROM l_location  HAVING distance < '.$radius.' ORDER BY distance  ';
			$resPost = Yii::$app->db->createCommand($query)->queryAll();
			foreach ($resPost as $res) {
					$CardStack[] = $res['locationIds'];
			}	
		} else {
			$CardStack = [];
        }
		$this->datas[self::DATAS] = $CardStack;
        //var_dump($query);die;
        return $this->datas; 
    }
    
    
    public function actionTagupdatetime()
    {  
        $CardStack = [];
		$query ='SELECT ids,updatetime FROM `l_updatetime` WHERE ids = '. self::TAG .' ORDER BY updatetime DESC LIMIT 1';
		$resPost = Yii::$app->db->createCommand($query)->queryAll();
		if ($resPost) {
			$tempcart = [];
			foreach ($resPost as $res) {
					$tempcart['id'] = $res['ids'];
					$tempcart['date'] = strtotime($res['updatetime']);
			}
			$CardStack[] = $tempcart;
		} else {
			$CardStack = [];
        }
		$this->datas[self::DATAS] = $CardStack;
        return $this->datas; 
    }
    
    public function actionTagkindupdatetime()
    {  
        $CardStack = [];
		$query ='SELECT ids,updatetime FROM `l_updatetime` WHERE ids = '. self::TAGK .' ORDER BY updatetime DESC LIMIT 1';
		$resPost = Yii::$app->db->createCommand($query)->queryAll();
		if ($resPost) {
			$tempcart = [];
			foreach ($resPost as $res) {
					$tempcart['id'] = $res['ids'];
					$tempcart['date'] = strtotime($res['updatetime']);
			}
			$CardStack[] = $tempcart;
		} else {
			$CardStack = [];
        }
		$this->datas[self::DATAS] = $CardStack;
        return $this->datas; 
    }

    public function actionLastversion()
    {  
        $request = Yii::$app->request;
        $os = $request->post('os');
        $version = $request->post('version');
        $CardStack = [];
        $query ='SELECT active,os FROM `l_version`';
		$resPost = Yii::$app->db->createCommand($query)->queryAll();
        
        if ($resPost) {
            $tempcart = [];
            foreach ($resPost as $res) {
                if ($os == $res['os']) { 
                    ($version == $res['active']) ? $tempcart['needUpdate'] = false: $tempcart['needUpdate'] = true;
                    $tempcart['version'] = $res['active'];
                    $tempcart['sort'] = 30;
                } else {
					$tempcart['needUpdate'] = false;
                    $tempcart['version'] = '';
                    $tempcart['sort'] = 30;
                }
            }
            $CardStack[] = $tempcart;
        } else {
            $CardStack = [];
        }
		$this->datas[self::DATAS] = $CardStack;
        return $this->datas; 
    }
    
   
    public function actionTagpackage()
    {  
         $request = Yii::$app->request;
         $packageIds = (int)$request->post('pack');
 		if ($packageIds >= 0 ) {
 			$query = 'SELECT tagIds as `id`, date, findName, name, tagkindIds as `tagkindId` FROM `l_tag`  ORDER BY  `tagIds` ASC LIMIT 1000  OFFSET '.(int)$packageIds.'000'; 
 			$resQuery = Yii::$app->db->createCommand($query)->queryAll();
 			if ($resQuery) {
 				foreach ($resQuery as $id) {
 					$tempcart = [];
 					foreach ($resQuery as $res) {
 						if ($id['id'] == $res['id']) {
 							$tempcart['id'] = $res['id'];
 							$tempcart[$this->date] = strtotime($res[$this->date]);
 							$tempcart['tagkindId'] = $res['tagkindId'];
 							$tempcart[$this->name] = $res[$this->name]; 
 							$tempcart[$this->findName] = $res[$this->findName];
 							$tempcart['sort'] = $this->sort;	
 						}	
 					}
 					$CardStack[] = $tempcart; 
 				}
 			}
         } else {
             $CardStack = [];
         } 
        //var_dump($this->Tagpackagecount());
 		$this->datas[self::DATAS] = $CardStack;
 		$this->datas['count'] = $this->Tagpackagecount();
        return $this->datas; 
    }
    
     public function actionTagpopular()
    {  
		$query = "(SELECT `tagIds` FROM `l_tag` WHERE name IN( 'видео', 'аудио' )) union (SELECT `tagIds` FROM `l_tag` ORDER BY RAND() LIMIT 70)"; 
		$resQuery = Yii::$app->db->createCommand($query)->queryAll();
		if ($resQuery) {
			foreach ($resQuery as $r) {
				foreach ($r as $id) {
						$CardStack[] = $id; 
				}
			}
		} else {
			$CardStack = [];
		} 
 		$this->datas[self::DATAS] = $CardStack;
        return $this->datas; 
    }
    
        
    public function actionAdd()
    {
		$arr = [];
		$newarr = [];
		for ($k=700000;$k < 900000; $k++) {
		$rand = rand ( 211111 , 299999 );
		$rand2 = rand ( 611111 , 699999 );
		$r = '('.$k.' , 56.'.$rand.' , 53.'.$rand2.')';
		$query ="INSERT INTO `l_location`(`locationIds`, `latitude`, `longitude`) VALUES".$r;
		//echo $query;
			//$resPost = Yii::$app->db->createCommand($query)->execute();
		//$arr[] = $k;
		}
		//shuffle($arr);
		//$tag = new Card();
		//$tag = new Card();
		//$resQuery = $tag::find()->all();
		//foreach ($resQuery as  $res) {
 
 		//	$newarr[] = $res['cardIds'];

			
		//}
		//var_dump($newarr);die;
		//$tag = new CardTag();
		//$resQuer = $tag::find()->all();
		
		//	foreach ($resQuer as $res) {
		//		$new = $tag::findOne($res['idCardTag']);
		//		$new->cardIds = array_pop ( $newarr );
				//$new->save();
				
				//$new->idCardStack = array_pop ( $newarr );
				//$new->save();
		//	}
			
    }
    /*
    
    
    */
    public function checkArray($array) 
    {
		$res = true;
		if(is_array($array)) {
			foreach ($array as $firstKey => $firstStep) {
				if (!is_numeric($firstKey)  ) {
					$res = false;
					}	
				if(is_array($firstStep)) {	
					foreach($firstStep as $secondKey => $secondStep) {
						
						if (!is_numeric($secondStep) || !is_numeric($secondKey) || is_array($secondStep) ) {
						$res = false;
						}
					}
				} else {
					$res = false;
				}
			}
		} else {
			$res = false;
		}
		return  $res;
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
     public function tagpackagecount()
    {  
        $request = Yii::$app->request;
        $os = $request->post('os');
        $query ='SELECT count(tagIds) as `count` FROM `l_tag`';
	$resPost = Yii::$app->db->createCommand($query)->queryAll();
        
        if ($resPost) {
            foreach ($resPost as $res) {

                    $CardStack = (int)($res['count']/1000)+1;
            }
            //$CardStack[] = $tempcart['count'];
        } else {
            $CardStack = [];
        }
        //var_dump($resPost);
	
        return $CardStack; 
    }
}