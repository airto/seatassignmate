<?php
if(!defined('TABLE_AIRPORT_CODES')){define('TABLE_AIRPORT_CODES','airport_codes');}
class Model_AirportCodes extends Zend_Db_Table
{
	protected $_name = TABLE_AIRPORT_CODES;
	
	private $AirportId;
	private $AirportCode;
	private $lantitude;
	private $longitude;
	
	function getLatLang($AirportCode){
		$select = $this->select()
			->from(array('ac'=>$this->_name),array('ac.*'))
			->where('AirportCode=?',$AirportCode);
		return $this->fetchRow($select);
	}
}
