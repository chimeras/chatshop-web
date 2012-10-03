<?php
abstract class Application_Model_Db_Table_ProductXLocations extends Zend_Db_Table_Abstract
{
	protected $_name = 'product_x_location';
	protected $_rowClass = 'Application_Model_ProductXLocation';
//	protected $_primary = 'product_x_location_id';
	protected $_primary = 'id';
/**
	 *
	 * @param integer $id
	 * @return Application_Model_Thread 
	 */
	public function fetch($id)
	{
		//return $this->fetchRow('product_x_location_id=' . $id);
		return $this->fetchRow('id=' . $id);
	}
} /*generated by setup*/