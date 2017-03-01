<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\Phonemaildata;
use yii\helpers\ArrayHelper;

class Data extends CommonLDC
{   
	public $tempArray = [];

	public function dataListAbout($ids,$infotype = false)
	{
		
	}
	
	public function dataListCompany($ids,$infotype)
    {
		$modelCompany = Company::findAll($ids);
		foreach($modelCompany as $company) {
			$list = [];
			$list['kind'] = $infotype;
			$list['title'] = '';
			$list['info'] = $company['company_anons'];
			$list['id'] = $company['company_id'];
			//$list['date'] = strtotime($company['event_date']);
			$list['name'] = $company['company_name'];
			$list['withDividers'] = true;
			$list['image'] = '';
			$list['fields'] = [
									[	'type' => 'button',
										'name' => 'Name', 
										'kind' => 'Page'
									],
									[
										'type' => 'separator',
									],
									[	'type' => 'text',
										'info' => 'text',
									],
									
									[
										'type' => 'separator',
									],
									
									[	'type' => 'next',
										'name' => 'Материалы', 
										'kind' => 'data\/resource',
										"id" => ''
									],
									
									[
										'type' => 'separator',
									],
									
									[	'type' => 'next',
										'name' => 'Материалы', 
										'kind' => 'data\/resource',
										"id" => ''
									],
									
									[	'type' => 'next',
										'name' => 'Nmae',
										'kind' => 'Page',
										'image' => '#'
									],
									[	'type' => 'info',
										'style' => 'Style',
										'image' => '#',
										'kind' => 'king'
									],
									[	'type' => 'group',
										'name' => 'Name',
										'style' => 'Style',
										'image' => 'icon',
										'kind' => 'kind',
										'fields' => [
											'type' => 'info',
											'style' => 'Style',
											'image' => '#',
											'kind' => 'king'
											]
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
										'kind' => 'page\/timeline\/4369\/4370',
										"id" => $event['gallery_gr_id']
									],
									
									
									[
										'type' => 'separator',
									],
									
									[	'type' => 'next',
										'name' => 'Материалы', 
										'kind' => 'data\/resource',
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
	
	
	public function dataListExpert($ids,$infotype)
    {
 		$modelPerson = Person::findAll($ids);
// 		$modelCompany = Company::findAll($ids);
 		foreach($modelPerson as $person) {
 			$list = [];
 			$listEvent = [];
 			$list['title'] = 'Эксперт';
 			$list['image'] = Yii::getAlias('@imgHost/zBoxuersk/gallery/'.$person['photo']);
 			$list['back'] = Yii::getAlias('@imgHost/zBoxuersk/gallery/'.$person['photo']);
 			$list['kind'] = $infotype;
 			$list['name'] = $person['surname'].' '.$person['name'].' '.$person['middlename'];
 			$list['id'] = $person['id'];
 			$list['withDividers'] = true;
 			foreach ($person->experts as $eventExpert) {
				$listEvent = [] ;
				$listEvent['type'] = 'event';
				$listEvent['name'] = $eventExpert['event_name'];
				$listEvent['kind'] = 'data\/event';
				$listEvent['hint'] = '';
				$listEvent['place'] = '';
				$listEvent['id'] = $eventExpert['event_id'];
				$listEvent['date'] = strtotime($eventExpert['event_date']);
				$list['fields'][] = $listEvent;
 			}
 			
//  			$list['fields'] = [
// 								[
// 									'type' => 'group',
// 									'name' => '',
// 									'style' => 'empty',
// 									'id' => 0,
// 									'fields' => [
// 													'type' => 'info',
// 													'name' => 'Програмно-информационное сообщество лейся программа прямо в компьютер на каждом шагу',
// 													'info' => 'Директор',
// 													'image' => 'http:\/\/o-planete.ru\/wp-content\/uploads\/2013\/05\/%D1%8D%D1%82%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B0%D1%8F-%D1%81%D1%82%D1%80%D1%83%D0%BA%D1%82%D1%83%D1%80%D0%B0-%D0%BD%D0%B0%D1%81%D0%B5%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F-.jpg',
// 													'kind' => 'data\/company',
// 													'style' => 'round',
// 													'id' => 203
// 												]
// 								],
// 								[
// 									'type' => 'text',
// 									'name' => '',
// 									'info' => 'Селитроваре лицеисту.\nФиллофорами обжалуешь фортификации откроил насурьмлённомся разопрелом олицетворить удаленькой измерительному оторопелым термофобах чаусы ссыпальщик препаровочную наростам бахчи.\nВымахивать полугодках водопольем отдерёмойся рвения меткости нездорового спазмолитическими проезжающим аппендикса.\nРубило сонетки перебрызганными неограниченности скидам побранивающимся расторговавшему хлорофосам.',
// 									'id' => 0
// 								],
// // 								[
// // 									'type' => 'event',
// // 									'name' => 'Мероприятие 1 года',
// // 									'kind' => 'data\/event',
// // 									'hint' => 'Деловой клубный вечер',
// // 									'place' => 'г. Ижевск ул. Воткинское Шоссе 220',
// // 									'id' => 501,
// // 									'date' => 2105656435
// // 								],
// // 								[
// // 									'type' => 'event',
// // 									'name' => 'Мероприятие 1 года',
// // 									'kind' => 'data\/event',
// // 									'hint' => 'Деловой клубный вечер',
// // 									'place' => 'г. Ижевск ул. Воткинское Шоссе 220',
// // 									'id' => 502,
// // 									'date' => 2105656435
// // 								],
// // 								[
// // 									'type' => 'event',
// // 									'name' => 'Мероприятие 1 года',
// // 									'kind' => 'data\/event',
// // 									'hint' => 'Деловой клубный вечер',
// // 									'place' => 'г. Ижевск ул. Воткинское Шоссе 220',
// // 									'id' => 503,
// // 									'date' => 2105656435
// // 								],
// 								[
// 									'type' => 'group',
// 									'name' => 'Галерея',
// 									'image' => 'http:\/\/s.androidinsider.ru\/2016\/11\/12Sea.@750.jpg',
// 									'kind' => 'album',
// 									'style' => 'hnext',
// 									'id' => 161,
// 									'fields' => [
// 													[
// 														'type' => 'info',
// 														'name' => '',
// 														'image' => 'https:\/\/encrypted-tbn2.gstatic.com\/images?q=tbn:ANd9GcQr2Ri6ZLeoOry_br1Rt4RVN5qFNnatxdiKMLNs82Jv5LAk4CCx',
// 														'kind' => 'photo\/https:\/\/encrypted-tbn2.gstatic.com\/images?q=tbn:ANd9GcQr2Ri6ZLeoOry_br1Rt4RVN5qFNnatxdiKMLNs82Jv5LAk4CCx',
// 														'style' => 'rect',
// 														'id' => 0
// 													],
// 													[
// 														'type' => 'info',
// 														'name' => '',
// 														'image' => 'https:\/\/encrypted-tbn2.gstatic.com\/images?q=tbn:ANd9GcR5jGG-uKd2Fiz6419sPP7NvxRw1kpzK61XXYkvyUO6hjGfap9U',
// 														'style' => 'rect',
// 														'id' => 0
// 														
// 													],
// 													[
// 														'type' => 'info',
// 														'name' => '',
// 														'image' => 'https:\/\/encrypted-tbn2.gstatic.com\/images?q=tbn:ANd9GcR5jGG-uKd2Fiz6419sPP7NvxRw1kpzK61XXYkvyUO6hjGfap9U',
// 														'style' => 'rect',
// 														'id' => 0
// 														
// 													],
// 									
// 												]
// 								]
// 
//  						];
 			$this->tempArray[] = $list;
 		}
 		return $list['fields'];//$this->tempArray;
    }
	
    public function dataListMy($ids)
    {
		//$list = Event::find()->where(['id' => $ids])->with('experts.persons')->one();

// 		if($listPerson) {
// 			$list = [];
// 			foreach($listPerson->experts as $per) {
// 				$tempArray = [];
// 				$tempArray['id'] = $per['idUser'];
// 				if($per->persons) {
// 					foreach($per->persons as $key => $res) {
// 						if($per['idUser'] == $res['id']) {
// 							$tempArray['image'] = $res['photo'];
// 							$tempArray['info'] = $res['descr'];
// 							$tempArray['name'] = $res['name'];
// 						} 
// 					}
// 				} else {
// 					$tempArray['image'] = '';
// 					$tempArray['info'] = '';
// 					$tempArray['name'] = '';
// 				}
// 				$tempArray['date'] = 0;
// 				$tempArray['hint'] = '';
// 				$tempArray['kind'] = '';
// 				$list[] = $tempArray;
// 			}
// 			return $list ;
// 		} else {
// 			return $list = [];
// 		}
		return $list = [];
    }
    
    public function dataListPerson($ids,$infotype)
    {
		$modelCompany = Person::findAll($ids);
		foreach($modelCompany as $company) {
			$list = [];
			$list['kind'] = $infotype;
			$list['title'] = '';
			$list['info'] = $company['descr'];
			$list['id'] = $company['id'];
			//$list['date'] = strtotime($company['event_date']);
			$list['name'] = $company['name'];
			$list['withDividers'] = true;
			$list['image'] = '';
			$list['fields'] = [
									[	'type' => 'button',
										'name' => 'Name', 
										'kind' => 'Page'
									],
									[
										'type' => 'separator',
									],
									[	'type' => 'text',
										'info' => 'text',
									],
									[	'type' => 'next',
										'name' => 'Nmae',
										'kind' => 'Page',
										'image' => '#'
									],
									[	'type' => 'info',
										'style' => 'Style',
										'image' => '#',
										'kind' => 'king'
									],
									[	'type' => 'group',
										'name' => 'Name',
										'style' => 'Style',
										'image' => 'icon',
										'kind' => 'kind',
										'fields' => [
											'type' => 'info',
											'style' => 'Style',
											'image' => '#',
											'kind' => 'king'
											]
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