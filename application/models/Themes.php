<?php

class Application_Model_Themes extends Application_Model_Db_Table_Themes
{

	protected $_name = 'theme';
    protected $_dependentTables = 'Application_Model_ThemeXCategorys';
	
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	public function fetchWithCategories()
	{
		
	}

}

