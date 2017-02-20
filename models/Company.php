<?php

namespace app\models;

//use Yii;

use yii\db\ActiveRecord;

class Company extends ActiveRecord 
{
// 	// логин пользователя
//  	public  $id; // идентификатор
// 	public $name;  //Название
// 	public $info;  //Описание
//     public $image;  //Картинка для Preview
//     public $back;  //Картинка для фона заголовка
//     public $title;  //Текст заголовка окна
// 	public $withDividers; // Между элементами есть разделитель


	 /**
     * @return string название таблицы, сопоставленной с этим ActiveRecord-классом.
     */
    public static function tableName()
    {
        //return "{{%company}}" ;
        return "company" ;
    }
    
	public function getPersons()
    {
        return $this->hasMany(Person::className(), ['id' => 'idPerson'])
						->viaTable('{{%companyPerson}}', ['idCompany' => 'company_id']);
    }

    public function getCompanyPersons()
    {
        return $this->hasMany(CompanyPerson::className(), ['idCompany' => 'company_id']);
    }

}