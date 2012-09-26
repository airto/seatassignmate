<?php
class Model_Cities extends Zend_Db_Table
{
	protected $_name = TABLE_CITIES;
	
	protected $city_id;
	protected $city_name;
	protected $city_state;
	protected $FK_CountryId;
	
	function getCity($cityId=0,$FK_CountryId=0){
		$select = $this->select();
		$select->where('1');
		//if($cityId > 0){$select->where('city_id=?',$cityId);}
		if($FK_CountryId > 0){
			if($this->fetchRow($this->select()->where('FK_CountryId=?',$FK_CountryId)))
				$select->where('FK_CountryId=?',$FK_CountryId);
		}
		return $this->fetchAll($select->order('city_name ASC'));
	}
	
}