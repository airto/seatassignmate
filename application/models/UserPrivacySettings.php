<?php
if(!defined('TABLE_USER_PRIVACY_SETTINGS')){define('TABLE_USER_PRIVACY_SETTINGS','user_privacy_settings');}
class Model_UserPrivacySettings extends Zend_Db_Table
{
	protected $_name = TABLE_USER_PRIVACY_SETTINGS;
	
	private $id;
	private $fkUserId;
	private $flightNumber;
	private $seatNumber;
	private $uniqueFlight;
	private $departureDate;
	private $privacyData;
	
	
	function getFlightPeople($uniqueFlight){
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('ups'=>$this->_name),array('ups.*'))
			->join(array('u'=>'tbl_users'),'u.UserId=ups.fkUserId',array('u.*'))
			->where('uniqueFlight is Not Null And uniqueFlight=?',$uniqueFlight)
			->order('ups.seatNumber');
		return $this->fetchAll($select);
	}
}
