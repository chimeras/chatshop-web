<?php
abstract class Application_Model_Db_Table_Users extends Application_Model_BaseCollection
{
	protected $_name = 'user';
	protected $_rowClass = 'Application_Model_User';
//	protected $_primary = 'user_id';
	protected $_primary = 'id';
/**
	 *
	 * @param integer $id
	 * @return Application_Model_User 
	 */
	public function fetch($id)
	{
		//return $this->fetchRow('user_id=' . $id);
		return $this->fetchRow('id=' . $id);
	}
} /*generated by setup*/