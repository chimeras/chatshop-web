<?php
abstract class Application_Model_Db_Table_Retailers extends Application_Model_BaseCollection
{
	protected $_name = 'retailer';
	protected $_rowClass = 'Application_Model_Retailer';
//	protected $_primary = 'retailer_id';
	protected $_primary = 'id';
/**
	 *
	 * @param integer $id
	 * @return Application_Model_Retailer 
	 */
	public function fetch($id)
	{
		//return $this->fetchRow('retailer_id=' . $id);
		return $this->fetchRow('id=' . $id);
	}
} /*generated by setup*/