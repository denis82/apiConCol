<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\UploadForm;
use app\models\UploadFile;

class Company extends ActiveRecord 
{
    public $dataResult = [];
    
    public function rules()
    {
        return 	[		
                    [['company_name'], 'required'],
                    [['company_address'], 'safe']
                ];
    }

    public function attributeLabels()
    {
        return [
            'company_name' => 'Название компании',
            'company_id' => 'идентификатор компании',
            'company_anons' => 'Анонс',
            'company_text'=> 'Текс',
            'company_image'=> 'Фото',
            'company_logo'=> 'Логотип',
        ];
    }

     /**
     * @return string название таблицы, сопоставленной с этим ActiveRecord-классом.
     */
     
    public static function tableName()
    {
        return "company" ;
    }
    
    public function getPersons()
    {
        return $this->hasMany(Person::className(), ['id' => 'idPerson'])
                        ->viaTable('{{%companyPerson}}', ['company_id' => 'company_id']);
    }

    public function getCompanyPersons()
    {
        return $this->hasMany(CompanyPerson::className(), ['idCompany' => 'company_id']);
    }
    
    public function updateCompany($idCompany)
    {
        $datas = [];
        if(!Yii::$app->request->post('fields')) {
            $fields = [];
        } else {
            $fields = Yii::$app->request->post('fields');
            $modelCompany = self::find()->where(['company_id' =>$idCompany])->one();
            $modelCompany->attributes = Yii::$app->request->post();
            $modelCompany->company_name = Yii::$app->request->post('name');
            if($modelCompany->validate()) {
                $modelCompany->save();
            } else {
                $datas[] = $modelCompany->errors;
            }
            $phoneMail = Phonemaildata::deleteAll(['idCompany' => $idCompany]); // обновление данных компании tbl phonemaildata
        }
        
        foreach($fields as $field) {
            $phoneMail = new Phonemaildata();
            $phoneMail->attributes = $field;
            $phoneMail->idCompany = $idCompany;
            
            if($phoneMail->validate()) {
                if(!$phoneMail->save()) {
                    $datas[] = 'Phonemaildata is not save';
                }
            } else {
                $datas[] = $phoneMail->errors;
            }	
        }
        return $datas;
    }
    
    /*
    /	Создает новую компанию и с привязкой к пользователю 
    /	вход 	-[string]$nameCompany
    /			-[int]$idUser
    /
    /	выход	- false/[int] company_id - ид компании либо 
    */	 
    
    public function createCompany($nameCompany,$idUser)
    {
        
        $this->company_name = $nameCompany;
            if($this->validate()) {
                if($this->save()) {
                    $companyPerson = new CompanyPerson();
                    $companyPerson->company_id = $this->company_id;
                    $companyPerson->idPerson = $idUser;
                    $companyPerson->save();
                    return $this->company_id;
                }
            } else {
                $datas["errors"] = $this->errors;
                return false;
            }
    }
    
    public function getPersonCompany($idCompany)
    {
        $idUser = Yii::$app->user->identity->getId();
        $exeptionFields = ['photo','surname','middlename'];
        
        $modelPerson = Person::findOne($idUser);
        $companyPerson = $modelPerson->companys;
        if($companyPerson) {
            $this->dataResult['datas'] = $this->companyInfo($idUser,$idCompany,$exeptionFields);
            $scope = [];
        }
        if(!empty($this->dataResult['datas'])) {
                $this->dataResult['success'] = true;
        }
        return $this->dataResult;
    }
    
    
    
    public function updatePersonCompany($idCompany)
    {
       // $idCompany = Yii::$app->request->post('id');
       //$this->tempArray = Yii::$app->request->post('fields');
        $idUser = Yii::$app->user->identity->getId();
        $modelCompany = self::findOne($idCompany);
        $modelUploadForm = new UploadForm();
        
        // если условие выполняется то данные компании обновятся 
        
        if(true != Yii::$app->request->post('create')) {    
            if(true == Yii::$app->request->post('imagefiledelete')) {   // если фотку компании нужно просто удалить
                $modelCompany->company_image = $modelUploadForm->deleteImg(Yii::$app->params['pathToFolderCompanyInWebSite'],$modelCompany->company_image);
            } else { // если фотку компании нужно загрузить
                $modelCompany->company_image = $modelUploadForm->uploadImg(Yii::$app->params['pathToFolderCompanyInWebSite'],$modelCompany->company_image);
            }
            $modelCompany->save();
        }
        
        // если условие выполняется компания будет создана 
        $res[] = Yii::$app->request->post('create');
        if(true == Yii::$app->request->post('create')) {              
            $idCompany = $this->createCompany(Yii::$app->request->post('name'),$idUser);
            $this->updateCompany($idCompany,Yii::$app->request->post());
            $this->company_image = $modelUploadForm->uploadImg(Yii::$app->params['pathToFolderCompanyInWebSite']);
            $this->save();
        }
        
        $this->dataResult['errors'] = $this->updateCompany($idCompany);
        $this->dataResult['datas'] = $this->companyInfo($idUser,$idCompany,['surname', 'middlename']);
        return $this->dataResult;
    }
    
    
    /*  Возвращяет подробный список сведений о компании
    /   вход:   $userInfo - [Integer] - id персоны
    /           $idCompany - [Integer] - id компании
    /           $exeption - [Array] поля которые не нужны
    /   выход:  [Array] 
    /                     данные о компании
    /   
    */

    public function companyInfo($userInfo = false,$idCompany = false,$exeption = [])
    {
        $arPhonemaildata = [];
        if($idCompany) {
            $companyInfo = Phonemaildata::findAll(['idCompany' =>$idCompany]);
            foreach($companyInfo as  $fields) {
                $arPhonemaildata[] = $fields;
            }
            $companyInfo = Company::findOne($idCompany);
            foreach($companyInfo as $key => $info) {
                //$tempArray[$key] = $info;
                //$tempArray['image'] = Url::to('@web/uploads/userAvatars/smallSize/',true) . '/' .$info;
            }
            //$tempArray =[];
            $tempArray['name'] = $companyInfo->company_name;
            $tempArray['logo'] = $companyInfo->company_logo;
            $tempArray['scope'] = '';//$companyInfo->company_name; 
            $tempArray['contact'] = '';//company_anons; 
            $tempArray['image'] = Yii::getAlias('@imgHost/zBoxuersk/company/') . $companyInfo->company_image;
            $tempArray['contact'] = $companyInfo->company_anons; 
            $tempArray['info'] = $companyInfo->company_anons;
        }   
            $tempArray['fields'] = $arPhonemaildata;
            if(empty($exeption)) {
                
                return $tempArray;
            } else {
                foreach($exeption as $exept) {
                    \yii\helpers\ArrayHelper::remove($tempArray, $exept);
                }
                return $tempArray;
            }
    }
}