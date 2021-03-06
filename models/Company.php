<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\UploadForm;
use app\models\UploadFile;

class Company extends ActiveRecord 
{
    public $dataResult = []; 
    public $idCompany;
    public $create;
    public $imagefiledelete;
    public $fields = [];
    public $myErrors = [];
    public $name;
    
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
    
    public function commonUpdateCompany() 
    {
        $checkUpdate = false;
        if ($this->updateCompany()) {
            if($this->deletePhonemaildata()) {
                if($this->updatePhonemaildata()) {
                    $checkUpdate = true;
                }
            }
        }
        return $checkUpdate;
    }
    
    public function updateCompany()
    {
        $update = false;
        $modelCompany = self::find()->where(['company_id' =>$this->idCompany])->one();
        $modelCompany->attributes = $this->attributes;
        $modelCompany->company_name = $this->name;
        if ($modelCompany->validate()) {
            $modelCompany->save();
        } else {
            $this->myErrors = $modelCompany->errors;
        }
        if (!$modelCompany->hasErrors()) {
            $update = true;
        }
        return $update;
    }
    
    public function deletePhonemaildata()
    {
        if(Phonemaildata::deleteAll(['idCompany' => $this->idCompany])) {
            return true;
        } else {
            return false;
        }
    }
    
    public function updatePhonemaildata()
    {
        $result = false;
        if(!empty($this->fields)) {
            foreach ($this->fields as $field) {
                $phoneMail = new Phonemaildata();
                $phoneMail->attributes = $field;
                $res = $phoneMail->attributes;
                $phoneMail->idCompany = $this->idCompany;  
                if ($phoneMail->validate()) {
                    $phoneMail->save();
                } else {
                    $this->myErrors = $phoneMail->errors;
                }
            }
            if (!$phoneMail->hasErrors()) {
                return true;
            }
        }  
        return $result;
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
            $this->dataResult['datas'][] = $this->companyInfo($idUser,$idCompany,$exeptionFields);
            $scope = [];
        }
        if(!empty($this->dataResult['datas'])) {
                $this->dataResult['success'] = true;
        }
        return $this->dataResult;
    }
    
    
    
    public function updatePersonCompany()
    {
        $idUser = Yii::$app->user->identity->getId();
        $modelCompany = self::findOne($this->idCompany);
        $modelUploadForm = new UploadForm();
        
        // если условие выполняется то данные компании обновятся 
        
        if (true != $this->create) {    
            if (true == $this->imagefiledelete) {   // если фотку компании нужно просто удалить
                $modelCompany->company_image = $modelUploadForm->deleteImg(Yii::$app->params['pathToFolderCompanyInWebSite'],$modelCompany->company_image);
            } else { // если фотку компании нужно загрузить
                $modelCompany->company_image = $modelUploadForm->uploadImg(Yii::$app->params['pathToFolderCompanyInWebSite'],$modelCompany->company_image);
            }
            $modelCompany->save();
        }
        
        // если условие выполняется компания будет создана 
        if (true == $this->create) {              
            $this->idCompany = $this->createCompany(Yii::$app->request->post('name'),$idUser);
            $this->commonUpdateCompany();
            $this->company_image = $modelUploadForm->uploadImg(Yii::$app->params['pathToFolderCompanyInWebSite']);
            $this->save();
        }
        
        
        $arResult = $this->commonUpdateCompany();
        $this->dataResult['datas'][] = $this->companyInfo($idUser,$this->idCompany,['surname', 'middlename']);
        if(!empty($this->dataResult['datas'])) {
                $this->dataResult['success'] = true;
        }
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
            $tempArray['id'] = $companyInfo->company_id;
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