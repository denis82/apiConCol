<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Phonemaildata extends ActiveRecord
{
	public function getPerson()
    {
        return $this->hasOne(Person::className(), ['id' => 'idPerson']);
    }
    
     public function rules()
	{
		return [
			['access', 'in', 'range' => [0, 1, 2]],
			['state', 'in', 'range' => [0, 1]],
			['group', 'integer'],
			['name', 'string', 'length' => [2]],
			['info', 'string', 'length' => [2]],
			['kind', 'string', 'length' => [2]],
			[['idPerson','idCompany','group'], 'integer'],
			['date','default', 'value' => '0000-00-00'],
			[['idPerson','idCompany','group','access','state'], 'default', 'value' => 0],
			[['name','info','kind'], 'default', 'value' => '']
		];
	}

	
    /**
     * @return string название таблицы, сопоставленной с этим ActiveRecord-классом.
     */
    public static function tableName()
    {
        return "{{%phonemaildata}}" ;
    }
    
    public function updatedatagroup($idUser,$idGroup,$fieldsArray)
	{
		$list = [];
		foreach($fieldsArray as $fields) {
 			
			if(!is_int($idGroup) and null == $idGroup) {
				continue;
			}
			$phoneMail = self::deleteAll(['idPerson' => $idUser,'group' => $idGroup]);
			$this->attributes = $fields;
			$this->idPerson = $idUser;
			$this->date = date('Y-m-d');
			
			if($this->validate()) {
				$arrQuery = $this->attributes;
				unset($arrQuery['id']);
				ksort($arrQuery);
				if($arrQuery['group'] == $idGroup) {
					$list[] = $arrQuery;
				} 
			}
 		}
 		$queryRes = Yii::$app->db->createCommand()->batchInsert('{{%phonemaildata}}', ['access','date','group','idCompany','idPerson','info','kind','name','state'], $list)->execute();
 		if(0 != $queryRes) {
			return $list;
		} else {
			return $list = [];
		}
	}
	
}