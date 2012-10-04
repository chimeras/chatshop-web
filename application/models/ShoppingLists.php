<?php
class Application_Model_ShoppingLists extends Application_Model_Db_Table_ShoppingLists
{
	protected $_dependentTables = 'Application_Model_ShoppingListItem';

	public function __construct($config = array())
	{
		parent::__construct($config);
	}
}