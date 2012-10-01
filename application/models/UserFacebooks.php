<?php

class Application_Model_UserFacebooks extends Application_Model_Db_Table_UserFacebooks
{

	protected $_referenceMap = array(
		'Users' => array(
			'columns' => array('user_id'), /*foreign key*/
			'refTableClass' => 'Application_Model_Users',
			'refColumns' => array('id') /*primary key of parent table*/
		)
	);

}

