<?php
if(!defined('TABLE_PRIVACY')){define('TABLE_PRIVACY','tbl_privacy');}
class Model_Privacy extends Zend_Db_Table
{
	protected $_name = TABLE_PRIVACY;
	
	private $PrivacyId;
	private $PrivacyName;
}
