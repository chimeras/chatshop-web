<?php
class Application_Model_Db_Table_UserInterests extends Zend_Db_Table_Abstract
{
	protected $_name = 'user_interest';
	protected $_rowClass = 'Application_Model_UserInterest';
	protected $_primary = 'user_interest_id';
/**
	 *
	 * @param integer $id
	 * @return Application_Model_Thread 
	 */
	public function fetch($id)
	{
		return $this->fetchRow('user_interest_id=' . $id);
	}
} /*generated by setup*/