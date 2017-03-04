<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\Phonemaildata;
use yii\helpers\ArrayHelper;

class Data extends CommonLDC
{   
	public $tempArray = [];
	public $getImgPath;

	public function dataListAbout($ids,$infotype = false)
	{
		
	}
	
	public function dataListCompany($ids,$infotype)
    {
		$modelCompany = Company::findAll($ids);
 		$serverName = Yii::$app->request->serverName;
 		$fullPath = $_SERVER['DOCUMENT_ROOT'];
 		$this->getImgPath = str_replace($serverName, "", $fullPath).Yii::$app->params['pathToFolderPersonInWebSite'];
		foreach($modelCompany as $company) {
			$list = [];
			$listPer = [];
			$fieldsPer = [];
			$list['kind'] = $infotype;
			$list['title'] = $company['company_name'];
			$list['info'] = $company['company_anons'];
			$list['id'] = $company['company_id'];
			//$list['back'] = "http:\/\/s.androidinsider.ru\/2016\/10\/android.@750.png";
			$list['name'] = $company['company_name'];
			$list['withDividers'] = false;
			$list['image'] = Yii::getAlias('@imgHost/images/company/'. $company['company_image']);
			foreach($company->persons as $person) {
					$listPer = [];
					$listPer['type'] = "info";
					$listPer['name'] = $person['surname'] . ' '.$person['name'];
					$listPer['info'] = $person['descr'];
					$listPer['image'] = Yii::getAlias('@imgHost/images/person/'. $person['photo']);
					$listPer['kind'] = "data/person";
					$listPer['style'] = "round";
					$listPer['id'] = $person['id'];
					$fieldsPer[] = $listPer;
			}
			$list['fields'] = 	[
									[
										"type" => "info",
										"name" => $company['company_name'],
										"info" => $company['company_anons'],
										"id" => 0
									],
									[
										"type" => "group",
										"name" => "",
										"style" => "empty",
										"id" => 0,
										"withDividers" => true,
										"fields" => 	$fieldsPer
										
									]
									
								];
								   
			$this->tempArray[] = $list;
		}
		return $this->tempArray;
    }
	
	public function dataListEvent($ids,$infotype)
	{
		$modelEvent = Event::findAll($ids);
		$button = 	[
						'type' => 'button',
						'name' => 'Registration2',
						'kind' => 'registration',
						'id' => 0
					];
		foreach($modelEvent as $event) {
			$list = [];
			$listImg = [];
			$fieldsImg = [];
			$list['kind'] = $infotype;
			$list['title'] = 'Мероприятие';
			$list['info'] = '';
			$list['id'] = $event['event_id'];
			$list['date'] = strtotime($event['event_date']);
			$list['name'] = $event['event_name'];
			
			$list['withDividers'] = false;//true; // если false то сепаратор есть на картинке 
			$tmpImg = array_slice(ArrayHelper::getColumn($event->galleries, 'gallery_image'), 0, 3);
			foreach($event->galleries as $img) {
				$listImg['type'] = 'info';
				$listImg['id'] = $img['gallery_id'];
				$listImg['image'] = Yii::getAlias('@imgHost/zBoxuersk/gallery/'.$img['gallery_image']);
				$listImg['kind'] = 'photo/'.Yii::getAlias('@imgHost/zBoxuersk/gallery/'.$img['gallery_image']);
				$listImg['style'] = 'rect';
				$fieldsImg[] = $listImg;
			}
			$fieldsImg = array_slice($fieldsImg, 0, 3);
			$list['image'] = $listImg;
			if($event['event_date']<date('Y-m-d')) {
				$button = [];
			}
			$list['fields'] = [
									[	'type' => 'group',
										'name' => '',
										'style' => 'head',
										"id" => $event['gallery_gr_id'],
										'fields' =>  $button
// 														[
// 															'type' => 'info',
// 															
// 															//'info' => 'г. Москва, Якиманский переулок, дом 6, Бизнес-центр \"Имперский дом\"',
// 															'kind' => 'map\/:г. Москва',
// 															'id' => 0
// 														]
													
									],
									
									[
										'type' => 'separator',
									],
									
									[	'type' => 'text',
										'name' => '', 
										'info' => $event['event_detail_text'],
										"id" => $event['gallery_gr_id']
									],
									
									[
										'type' => 'separator',
									],
									
									[	'type' => 'next',
										'name' => 'Расписание', 
										'kind' => 'page/timeline\/4369\/4370',
										"id" => $event['gallery_gr_id']
									],
									
									
									[
										'type' => 'separator',
									],
									
									[	'type' => 'next',
										'name' => 'Материалы', 
										'kind' => 'data/resource',
										"id" => $event['event_id']
									],
									
									[
										'type' => 'separator',
									],
									[
										'type' => 'group',
										'name' => 'Галерея',
										'image' => 'http:\/\/s.androidinsider.ru\/2016\/11\/12Sea.@750.jpg',
										'kind' => 'album',
										'style' => 'hnext',
										'id' => $event['gallery_gr_id'], // id_album
										'fields' => $fieldsImg
									],
									
									
								   ];
			$this->tempArray[] = $list;
		}
		return $this->tempArray;
	}
    
    public function dataListPerson($ids,$infotype)
    {
		$arExpert = [];
		$arPhoto = [];
 		$modelPerson = Person::findAll($ids);
 		foreach($modelPerson as $person) {
 			$list = [];
 			$listEvent = [];
 			$list['title'] = $person['surname'].' '.$person['name'].' '.$person['middlename'];
 			if ('expert' == $infotype) {
				$list['title'] = 'Эксперт';
			}
 			$list['image'] = Yii::getAlias('@imgHost/zBoxuersk/gallery/'.$person['photo']);
 			$list['back'] = Yii::getAlias('@imgHost/zBoxuersk/gallery/'.$person['photo']);
 			$list['kind'] = $infotype;
 			$list['name'] = $person['surname'].' '.$person['name'].' '.$person['middlename'];
 			$list['id'] = $person['id'];
 			$list['withDividers'] = true;
 			$arExpert = $this->experts($person->experts);
 			$arPhoto = $this->personphotos($person->personphotos);
 			$list['fields'] = [
								[
									'type' => 'group',
									'name' => '',
									'style' => 'empty',
									'id' => 0,
									'fields' => [	
													[
													'type' => 'info',
													'name' => 'Програмно-информационное сообщество лейся программа прямо в компьютер на каждом шагу',
													'info' => 'Директор',
													'image' => 'http:\/\/o-planete.ru\/wp-content\/uploads\/2013\/05\/%D1%8D%D1%82%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B0%D1%8F-%D1%81%D1%82%D1%80%D1%83%D0%BA%D1%82%D1%83%D1%80%D0%B0-%D0%BD%D0%B0%D1%81%D0%B5%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F-.jpg',
													'kind' => 'data/company',
													'style' => 'round',
													'id' => $person->companyid[0]["idCompany"],
													]
												]
								],
								[
									'type' => 'text',
									'name' => '',
									'info' => 'Селитроваре лицеисту.\nФиллофорами обжалуешь фортификации откроил насурьмлённомся разопрелом олицетворить удаленькой измерительному оторопелым термофобах чаусы ссыпальщик препаровочную наростам бахчи.\nВымахивать полугодках водопольем отдерёмойся рвения меткости нездорового спазмолитическими проезжающим аппендикса.\nРубило сонетки перебрызганными неограниченности скидам побранивающимся расторговавшему хлорофосам.',
									'id' => 0
								],
								$arExpert,
								[
									'type' => 'group',
									'name' => 'Галерея',
									'image' => Yii::getAlias('@imgHost/zBoxuersk/gallery/'.$person['photo']),
									'kind' => 'album',
									'style' => 'hnext',
									'id' => $person['id'],
									'fields' => $arPhoto,
								]

 						];
 			$this->tempArray[] = $list;
 		}
 		return $this->tempArray;
    }   
    
    public function dataListResource($ids,$infotype = false)
    {
		$listExpert = Event::find()->where(['id' => $ids])->with('experts.persons')->one();
		if($listExpert) {
			$list = [];
			foreach($listExpert->experts as $per) {
				$tempArray = [];
				$tempArray['id'] = $per['idPerson'];
				if($per->persons) {
					foreach($per->persons as $key => $res) {
						if($per['idPerson'] == $res['id']) {
							$tempArray['image'] = $res['photo'];
							$tempArray['info'] = $res['descr'];
							$tempArray['name'] = $res['name'];
						} 
					}
				} else {
					$tempArray['image'] = '';
					$tempArray['info'] = '';
					$tempArray['name'] = '';
				}
				$tempArray['date'] = 0;
				$tempArray['hint'] = '';
				$tempArray['kind'] = '';
				$list[] = $tempArray;
			}
			return  $list ;
		} else {
			return $list = [1];
		}
    }
    
    private function personphotos($personphotos) 
    {
		$arPhoto = [];
		foreach($personphotos as $photo) {
			$listPhoto = [];
			$listPhoto['type'] = 'info';
			$listPhoto['name'] = $photo['gallery_name'];
			$listPhoto['image'] = Yii::getAlias('@imgHost/zBoxuersk/gallery/'.$photo['gallery_image']);
			$listPhoto['kind'] = 'photo/' . Yii::getAlias('@imgHost/zBoxuersk/gallery/'.$photo['gallery_image']);
			$listPhoto['style'] = 'rect';
			$listPhoto['id'] = $photo['gallery_id'];
			$arPhoto[] = $listPhoto;
		}
 		return 	$arPhoto;
    }
    private function experts($experts) 
    {
		$arExpert = [];
		foreach ($experts as $eventExpert) {
			$listEvent = [] ;
			$listEvent['type'] = 'event';
			$listEvent['name'] = $eventExpert['event_name'];
			$listEvent['kind'] = 'data\/event';
			$listEvent['hint'] = '';
			$listEvent['place'] = '';
			$listEvent['id'] = $eventExpert['event_id'];
			$listEvent['date'] = strtotime($eventExpert['event_date']);
			$arExpert[] = $listEvent;
		}
		return $arExpert;
 	}		
    
    public function getImagePath() 
    {
    
    }
}

//  id   - [Integer] идентификатор
//  name - [String] Название
//  info - [String] Описание
//  image - [String] Картинка для Preview
//  back - [String] Картинка для фона заголовка
//  title - [String] Текст заголовка окна
//  date - [UNIX Time][Optional] время начала [02.02.2017]
//  withDividers - [Boolean] true: Между элементами есть разделитель
//  fields - [Array[KindModel]]  все возможные поля для отображения разных типов
//  kind - [String] Тип отображаемых данных (Person/Event)