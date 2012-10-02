<?php
class Application_Model_ShoppingList extends Application_Model_Db_Row_ShoppingList
{
	const VISIBILITY_PUBLIC = 1;
	const VISIBILITY_PRIVATE = 2;
	const STATE_ACTIVE = 1;
	const STATE_ARCHIVED = 2;
	
	
	public function __construct(array $config = array())
	{
		parent::__construct($config);
	}
}