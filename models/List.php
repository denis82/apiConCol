<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\Phonemaildata;

class Listing extends Model
{
    
    /**
     * @return array the validation rules.
     */
	 public function rules()
	{
		return [
		
			[['middlename','surname','name'], 'string', 'length' => [2, 35]],
			[['city','country'], 'string', 'length' => [2]],
			['photo','image','extensions' => 'png, jpg'],
			[['middlename',], 'default', 'value' => '']

		];
	}
	
	public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'surname' => 'Фамилия',
			'middlename' => 'Отчество',
			'city'=> 'Город',
			'country'=> 'Страна',
			'photo'=> 'Фото',
			'descr'=> 'Описание',
			'welcome'=> 'Приветствие',
			'blog'=> 'Блог',
			'status'=> 'Статус',
        ];
    }
    
    public function listOfSomething($action,$infotype,$ids = [])
    {	
		switch($action){
			case 'Person':
				switch($infotype){
					case 'Company':
						$list = $this->PersonCompany($action,$infotype,$ids);
						break;
					case 'Event':
						$list = $this->PersonEvent($action,$infotype,$ids);
						break;
					default:
						break;
				}
				break;
			case 'Event':
				switch($infotype){
					case 'Expert':
						$list = $this->EventExpert($action,$infotype,$ids);
						break;
					case 'Company':
						$list = $this->EventCompany($action,$infotype,$ids);
						break;
					case 'Person':
						$list = $this->EventPerson($action,$infotype,$ids);
						break;
					default:
						break;
				}
				break;
			case 'Company':
				switch($infotype){
					case 'Person':
						$list = $this->CompanyPerson($action,$infotype,$ids);
						break;
					default:
						break;
				}
			break;
			case 'My':
				switch($infotype){
					case 'Person':
						$list = $this->MyPerson($action,$infotype,$ids);
						break;
					case 'Company':
						$list = $this->MyCompany($action,$infotype,$ids);
						break;
					case 'Event':
						$list = $this->MyEvent($action,$infotype,$ids);
						break;	
					default:
						break;
				}
			break;
		}
// 		$list = self::findAll([
// 		'access' => self::ACCESS_PUBLIC]);
		return $list;
	}
	public function PersonCompany($action,$infotype,$ids)
    {
		$listPerson = Person::findAll($ids);
		return $listPerson;
    }
    public function PersonEvent($action,$infotype,$ids)
    {
		
    }
    public function EventPerson($action,$infotype,$ids)
    {
		
    }
}