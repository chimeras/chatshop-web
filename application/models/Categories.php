<?php

class Application_Model_Categories extends Application_Model_Db_Table_Categories
{

	protected $_name = 'category';

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

}

