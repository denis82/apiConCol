<?php

namespace app\models;

//use Yii;

use yii\db\ActiveRecord;

class Company extends ActiveRecord 
{
// 	const SCENARIO_UPDATE = 'update_company';
// 	
// 	public function scenarios()
//     {
//         return [
//             self::SCENARIO_UPDATE => ['name'],
//         ];
//     }

	 public function rules()
	{
		return 	[		
					[['company_name'], 'required']
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