<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\myclass\Clearstr;
use yii\helpers\ArrayHelper;
use app\models\Phonemaildata;

class Data extends CommonLDC
{   
	public $tempArray = [];
	public $getImgPath;

	/**
     *  Выбирает функцию "обработчик" в зависимости от значения typeinfo
     *
     * @param array   - $ids  ид инфотипа
     * @param string  - $infotype - инфотип(см. комменты перед DataController)
     * @return array 
     *                  
     */
     
	public function infotypeSwitch($ids,$infotype) {
        switch($infotype){
            case 'expert':
                $this->tempArray = $this->dataListPerson($ids,$infotype);  // список экспертов dataListExpert
                break;
            case 'event':
                $this->tempArray = $this->dataListEvent($ids,$infotype); // список событий  dataListEvent
                break;
            case 'company':
                $this->tempArray = $this->dataListCompany($ids,$infotype); // список компаний  dataListCompany
                break;
            case 'about':
                $this->tempArray = $this->dataListAbout(); // Страница о программе  dataListAbout
                break;
            case 'resource':
                $this->tempArray = $this->dataListResource($ids,$infotype); // список ресурсов(презентации к мероприятию)  dataListResourse
                break;
            case 'person':
                $this->tempArray = $this->dataListPerson($ids,$infotype); // список персон  dataListPerson
                break;
            case 'my':
                $ids = [];
                
                if(!$idUser = Yii::$app->user->isGuest){
                    $ids[] = Yii::$app->user->identity->getId();
                } 
                $this->tempArray = $modelData->dataListPerson($ids,$infotype); // мой список   dataListMy
                break;
            default:
                break;  
        }
        return $this->tempArray;
    }
 

    public function dataListAbout()
    {
        $about =
        [
            "title" =>  "О Конгресс-коллегии",
            "image" =>  "about.png",
            "kind" =>  "about",
            "name" =>  "Конгресс-Коллегия",
            "id" => 1,
            "withDividers" => false,
            "fields" => [
                        [
                            "type" =>  "text",
                            "name" =>  "",
                            "info" =>  "Бизнес-сообщество",
                            "style" =>  "center"
                        ],
                        [
                            "type" =>  "group",
                            "name" =>  "",
                            "style" =>  "empty",
                            "withDividers" => true,
                            "fields" => [
                                [
                                "type" =>  "phone_email",
                                "name" =>  "",
                                "info" =>  "www.con-col.com",
                                "kind" =>  "site"
                                ],
                                [
                                "type" =>  "phone_email",
                                "name" =>  "",
                                "info" =>  "+7 (909) 955-92-30",
                                "kind" =>  "phone"
                                ],
                                [
                                "type" =>  "phone_email",
                                "name" =>  "",
                                "info" =>  "info@con-col.com",
                                "kind" =>  "email"
                                ]
                            ]
                        ],
                        [
                            "type" =>  "separator",
                            "name" =>  "",
                            "style" =>  "small"
                        ],
                        [
                            "type" =>  "text",
                            "name" =>  "Инициатры создания:",
                            "info" =>  "Группа физических лиц, заинтересованных в создании площадки для эффективного решения своих индивидуальных и коллегиальных задач."
                        ],
                        [
                            "type" =>  "text",
                            "name" =>  "Основная цель:",
                            "info" =>  "«Конгресс-коллегия» призвана объединить представителей делового мира, культуры и науки, по-литиков и общественных деятелей, разделяющих общие гуманитарные ценности и реализующих инди-видуальные и коллегиальные деловые и общественно-значимые задачи."
                        ],
                        [
                            "type" =>  "text",
                            "name" =>  "Первоочередными задачами «Конгресс-коллегии» являются:",
                            "info" =>  "ормирование заинтересованной консолидированной Аудитории;",
                            "style" =>  "hyphen"
                        ],
                        [
                                                    "type" =>  "text",
                                                    "name" =>  "",
                                                    "info" =>  "организация востребованных членами сообщества мероприятий, разнообразных по форме и тематике;",
                                                    "style" =>  "hyphen"
                        ],
                        [
                                                    "type" =>  "text",
                                                    "name" =>  "",
                                                    "info" =>  "реализация совместных коммерческих и гуманитарных проектов, инициированных членами сообщества;",
                                                    "style" =>  "hyphen"
                        ],
                        [
                                                    "type" =>  "text",
                                                    "name" =>  "",
                                                    "info" =>  "обеспечение каждому члену сообщества возможности представления своих интересов и инициатив.",
                                                    "style" =>  "hyphen"
                        ]
            ]
        ];
        
    return $about;
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
            $list['fields'] =   [
                                    [
                                        "type" => "text",
                                        //"name" => $company['company_name'],
                                        "info" => $company['company_anons'],
                                        "id" => 0
                                    ],
                                    [
                                        "type" => "group",
                                        "name" => "",
                                        "style" => "empty",
                                        "id" => 0,
                                        "withDividers" => true,
                                        "fields" => $this->getArrCompanyPerson($company->persons)
                                    ]

                                ];

            $this->tempArray[] = $list;
        }
    return $this->tempArray;
    }

    public function dataListEvent($ids,$infotype)
    {
        $modelEvent = Event::findAll($ids);
        foreach($modelEvent as $event) {
            $list = [];
            $listImg = [];
            $list['kind'] = $infotype;
            $list['title'] = 'Мероприятие';
            $list['info'] = '';
            $list['id'] = $event['event_id'];
            $list['date'] = strtotime($event['event_date']);
            $list['name'] = str_replace( "­", "", $event['event_name']);
            $list['withDividers'] = false;//true; // если false то сепаратор есть на картинке 
            $tmpImg = array_slice(ArrayHelper::getColumn($event->galleries, 'gallery_image'), 0, 3);
            $list['image'] = $listImg;
            $button = $this->getButton($event['event_date']);
            $seporator = [];
            $arGallery = [];
            $arList = [];
            if($event['gallery_gr_id']) {
                $seporator = [
                                'type' => 'separator',
                             ];
                $arGallery = [
                                "type" => "next",
                                "name" => "Галерея",
                                "kind" => "album",
                                "id" => $event['gallery_gr_id']
                             ];
                $arList = [
                                "type" => "list",
                                "name" => "",
                                "kind" => "all/photo",
                                "style" => "default",
                                "id" => $event['gallery_gr_id'],
                                "itemStyle" => "rect",
                                "fields" => $this->getArrEventGalleries($event->galleries)//$fieldsImg
                           ];
            }
            $list['fields'] = [
                                [	
                                    'type' => 'group',
                                    'name' => '',
                                    'style' => 'head',
                                    "id" => $event['gallery_gr_id'],
                                    'fields' =>  $button
                                ],

//                                 [
//                                     'type' => 'separator',
//                                 ],
// 
//                                 [	'type' => 'text',
//                                     'name' => '', 
//                                     'info' => $event['event_detail_text'],//$event_detail_text,//str_replace("&shy;", '',$event['event_detail_text']),
//                                     "id" => $event['gallery_gr_id']
//                                 ],
// 
//                                 [
//                                     'type' => 'separator',
//                                 ],
//                                 [
//                                     'type' => 'next',
//                                     'name' => 'Материалы', 
//                                     'kind' => 'data/resource',
//                                     "id" => $event['event_id']
//                                 ],
                                $seporator
                                ,
                                $arGallery
                                ,
                                $arList
                                ,


                ];
                $list['fields'] = $this->arrFilter($list['fields']);
                
            $this->tempArray[] = $list;
        }
        return $this->tempArray;
    }
    
    
    /**
    * Отфильтрует массив от пустых заначений типа array()
    * @param array массив для фильтрации
    * @return array
    */
    
    private function arrFilter($arr)
    {
        $temp = [];
        foreach($arr as $val) {
            if(!empty($val)) {
                $temp[] = $val;
            }
        }
        return $temp;
    }
    
    public function dataListPerson($ids,$infotype)
    {
        $arExpert = [];
        $arPhoto = [];
        $modelPerson = Person::find()->with(['companys', 'phonemaildatas','companyid'])
                                            ->where(['id' => $ids])
                                            ->all();
        foreach($modelPerson as $person) {
            $list = [];
            $listEvent = [];
            $list['title'] = $person['surname'].' '.$person['firstname'].' '.$person['middlename'];
            if ('expert' == $infotype) {
                $list['title'] = 'Эксперт';
            }
            
            $list['image'] = Yii::getAlias('@imgHost/zBoxuersk/position_author/'.$person['position_author_image']);
            $list['back'] = Yii::getAlias('@imgHost/zBoxuersk/gallery/'.$person['photo']);
            $list['kind'] = $infotype;
            $list['name'] = $person['surname'].' '.$person['firstname'].' '.$person['middlename'];
            $list['id'] = $person['id'];
            $list['withDividers'] = true;
            $arExpert = $this->experts($person->experts);
            $arPhoto = $this->personphotos($person->personphotos);
            $arCompanys = $this->companys($person);
            
            $arComp = [];
            $pers = [];
            $exp = [];
            $gallery = [];
            
            if($arCompanys) {
                $arComp = [
                            'type' => 'group',
                            'name' => '',
                            'style' => 'empty',
                            'id' => 0,
                            'fields' => $arCompanys
                                        
                        ];
            }
            if($person['welcome']) {
                $pers = [
                            'type' => 'text',
                            'name' => '',
                            'info' => $person['welcome'],
                            'id' => 0
                            ];
            }
            if(!empty($arExpert)) {
                $exp = [
                            'type' => 'group',
                            'fields' => $arExpert,
                        ];
            }
            if($person['photo'] || !empty($arPhoto)) {
                $gallery = [
                            
                            
                            [   
                                "type" => "next",
                                "name" => "Галерея",
                                "kind" => "album",
                                "id" => $person['id']
                            ],
                            [
                                "type" => "list",
                                "name" => "",
                                "kind" => "all/photo",
                                "style" => "default",
                                "id" => $person['id'],
                                "itemStyle" => "rect",
                                "fields" => $arPhoto
                            ]
                            
                            
//                             'type' => 'group',
//                             'name' => 'Галерея',
//                             'image' => Yii::getAlias('@imgHost/zBoxuersk/gallery/'.$person['photo']),
//                             //'kind' => 'album',
//                             'style' => 'hnext',
//                             'id' => $person['id'],
//                             'fields' => $arPhoto,
                        ];
            }
            $list['fields'] = [$arComp,$pers,$exp,$gallery];
            $this->tempArray[] = $list;
    /*
    * {
        "type": "next",
        "name": "Галерея",
        "kind": "album",
        "id": 146
    },
    {
        "type": "list",
        "name": "",
        "kind": "all/photo",
        "style": "default",
        "id": 146,
        "itemStyle": "rect",
        "fields": [
            {
                "type": "info",
                "name": "",
                "image": "https:\/\/encrypted-tbn2.gstatic.com\/images?q=tbn:ANd9GcQr2Ri6ZLeoOry_br1Rt4RVN5qFNnatxdiKMLNs82Jv5LAk4CCx",
                "id": 7
            },
            {
                "type": "info",
                "name": "",
                "image": "https:\/\/encrypted-tbn2.gstatic.com\/images?q=tbn:ANd9GcR5jGG-uKd2Fiz6419sPP7NvxRw1kpzK61XXYkvyUO6hjGfap9U",
                "id": 8
            }
        ]
    }
    */                        
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
                            $tempArray['name'] = $res['firstname'];
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
        return $arPhoto;
    }
    private function experts($experts) 
    {
        $arExpert = [];
        foreach ($experts as $eventExpert) {
            $listEvent = [] ;
            $listEvent['type'] = 'event';
            $listEvent['name'] = Clearstr::clear($eventExpert['event_name']);
            $listEvent['kind'] = 'data/event';
            $listEvent['hint'] = '';
            $listEvent['place'] = '';
            $listEvent['id'] = $eventExpert['event_id'];
            $listEvent['date'] = strtotime($eventExpert['event_date']);
            $arExpert[] = $listEvent;
        }
        return $arExpert;
    }
    
    private function companys($personInfo) 
    {
        $comp = [];
            foreach ($personInfo->companyid as $person) {
                $info = [];
                $tempArray = [];
                $tempArray['type'] = 'info';
                $tempArray['kind'] = 'data/company';
                $tempArray['style'] = 'round';
                foreach($personInfo->companys as $company) {
                    if ($person['company_id'] == $company['company_id']) {
                        $tempArray['id'] = $company['company_id'];
                        $tempArray['name'] = Clearstr::clear($company['company_name']);
                        $tempArray['image'] = Yii::getAlias('@imgHost/zBoxuersk/company/' . $company['company_image']);
                        $info[0] = $company['company_name'];
                        $info[2] = Clearstr::clear($company['company_anons']);
                    }
                }
                $info[1] = $person['position'];
                ksort($info); 
                $tempArray['info'] = '';//implode("\n", $info);
                $comp[] = $tempArray;
            }
            return $comp;	
        
    }
    
    private function getArrCompanyPerson($persons)
    {
        $fieldsPer = [];
        foreach($persons as $person) {
            $listPer = [];
            $listPer['type'] = "info";
            $listPer['name'] = $person['surname'] . ' '.$person['firstname'];
            $listPer['info'] = $person['descr'];
            $listPer['image'] = Yii::getAlias('@imgHost/images/person/'. $person['photo']);
            $listPer['kind'] = "data/person";
            $listPer['style'] = "round";
            $listPer['id'] = $person['id'];
            $fieldsPer[] = $listPer;
        }
        return $fieldsPer;
    }
    
    private function getArrEventGalleries($galleries)
    {
        $fieldsImg = [];
        foreach($galleries as $img) {
            $listImg['type'] = 'info';
            $listImg['id'] = $img['gallery_id'];
            $listImg['image'] = Yii::getAlias('@imgHost/zBoxuersk/gallery/'.$img['gallery_image']);
            $fieldsImg[] = $listImg;
        } 
        $fieldsImg = array_slice($fieldsImg, 0, 3);
        return $fieldsImg;
    }
    
    private function getButton($eventDate)
    {
        $button =   [
                        'type' => 'button',
                        'name' => 'Registration2',
                        'kind' => 'registration',
                        'id' => 0
                    ];
        if($eventDate < date('Y-m-d')) {
            $button = [];
        }
        return $button;
    }
    
    public function getImagePath() 
    {
    
    }
}
