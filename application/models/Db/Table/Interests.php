<?php
abstract class Application_Model_Db_Table_Interests extends Application_Model_BaseCollection
{
	protected $_name = 'interest';
	protected $_rowClass = 'Application_Model_Interest';
//	protected $_primary = 'interest_id';
	protected $_primary = 'id';
/**
	 *
	 * @param integer $id
	 * @return Application_Model_Interest 
	 */
	public function fetch($id)
	{
		//return $this->fetchRow('interest_id=' . $id);
		return $this->fetchRow('id=' . $id);
	}
} /*generated by setup*/