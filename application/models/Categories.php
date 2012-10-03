<?php

class Application_Model_Categories extends Application_Model_Db_Table_Categorys
{

	protected $_name = 'category';
	protected $_dependentTables = 'Application_Model_ThemeXCategories';

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

}

