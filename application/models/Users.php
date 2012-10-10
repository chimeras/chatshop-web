<?php

class Application_Model_Users extends Application_Model_Db_Table_Users
{
	protected $_dependentTables = 'Application_Model_ShoppingLists';
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function fetchBySession($value)
	{
		return $this->fetchRow("session LIKE'" . $value . "'");
	}

	
	
}

