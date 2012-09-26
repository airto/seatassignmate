<?php
if(!defined('TABLE_USERS')){define('TABLE_USERS','tbl_users');}
class Model_User extends Zend_Db_Table
{
	protected $_name = TABLE_USERS;
	
	private $UserId;
	private $firstName;
	private $lastName;
	private $email;
	private $facebookId;
	private $linkedinId;
	private $pic;
	private $password;
	
	public function setUserId($userId){ $this->UserId = $userId;}
	public function getUserId($userId){ return $this->UserId;}
	
	public function setFirstName($fname){ $this->firstName = $fname;}
	public function getFirstName(){ return $this->firstName;}
	
	public function setLastName($lname){ $this->lastName = $lname;}
	public function getLastName(){ return $this->lastName;}
	
	public function setEmail($email){ $this->email = $email;}
	public function getEmail(){ return $this->email;}
	
	public function setFacebookId($fbId){ $this->facebookId = $fbId;}
	public function getFacebookId(){ return $this->facebookId;}
	
	public function setLinkedinId($linId){ $this->linkedinId = $linId;}
	public function getLinkedinId(){ return $this->linkedinId;}
	
	public function setPic($pic){$this->pic = $pic;}
	public function getPic(){ return $this->pic;}
	
	public function setPassword($password){$this->password = md5($password);}
	public function getPassword(){ return $this->password;}
	
	public function checkUser($formData) {
	
	}
	
	public function insertRow($formData) { 
		$row				=	$this->CreateRow();
		$row->firstName			=	addslashes($formData['fname']);
		$row->lastName			=	$formData['lname'];
		$row->email		=	$formData['email'];
		return $row->save();
	}	
}