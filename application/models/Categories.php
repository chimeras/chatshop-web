<?php

class Application_Model_Categories extends Application_Model_Db_Table_Categories
{

	protected $_name = 'category';
	protected $_referenceMap = array(
		'Category' => array(
			'columns' => array('parent_id'), /* foreign key */
			'refTableClass' => 'Application_Model_Categories',
			'refColumns' => array('id') /* primary key of parent table */
		)
	);

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

}

