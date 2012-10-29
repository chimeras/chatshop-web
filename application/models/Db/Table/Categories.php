<?php
abstract class Application_Model_Db_Table_Categories extends Application_Model_BaseCollection
{
	protected $_name = 'category';
	protected $_rowClass = 'Application_Model_Category';
//	protected $_primary = 'category_id';
	protected $_primary = 'id';
/**
	 *
	 * @param integer $id
	 * @return Application_Model_Category 
	 */
	public function fetch($id)
	{
		//return $this->fetchRow('category_id=' . $id);
		return $this->fetchRow('id=' . $id);
	}
} /*generated by setup*/