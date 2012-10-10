<?php

class Application_Model_ShoppingLists extends Application_Model_Db_Table_ShoppingLists
{

	protected $_dependentTables = 'Application_Model_ShoppingListItem';
	protected $_referenceMap = array(
		'Users' => array(
			'columns' => array('user_id'), /* foreign key */
			'refTableClass' => 'Application_Model_Users',
			'refColumns' => array('id') /* primary key of parent table */
		)
	);

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

}