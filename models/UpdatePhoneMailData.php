<?php

namespace app\models;

use yii\db\ActiveRecord;

class UpdatePhoneMailData extends Model
{


	
	//public id;
	public $idPerson;
	public $date;
	public $name;
	public $info;
	public $kind;
	public $access;
	public $state;
	public $group;
	public $idCompany;
    
 public function rules()
	{
		return [
			['date','date'],
			['access', 'in', 'range' => [0, 1, 2]],
			['state', 'in', 'range' => [0, 1]],
			['name', 'string', 'length' => [2]],
			['info', 'string', 'length' => [2]],
			['kind', 'string', 'length' => [2]],
			[['idPerson','idCompany','group'], 'integer']
		];
	}
	
    /**
     * @return string название таблицы, сопоставленной с этим ActiveRecord-классом.
     */
    public static function tableName()
    {
        return "{{%phonemaildata}}" ;
    }
    
}