<?php
abstract class Application_Model_Db_Table_Pictures extends Application_Model_BaseCollection
{
	protected $_name = 'picture';
	protected $_rowClass = 'Application_Model_Picture';
//	protected $_primary = 'picture_id';
	protected $_primary = 'id';
/**
	 *
	 * @param integer $id
	 * @return Application_Model_Picture 
	 */
	public function fetch($id)
	{
		//return $this->fetchRow('picture_id=' . $id);
		return $this->fetchRow('id=' . $id);
	}
} /*generated by setup*/