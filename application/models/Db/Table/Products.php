<?php
abstract class Application_Model_Db_Table_Products extends Zend_Db_Table_Abstract
{
	protected $_name = 'product';
	protected $_rowClass = 'Application_Model_Product';
//	protected $_primary = 'product_id';
	protected $_primary = 'id';
/**
	 *
	 * @param integer $id
	 * @return Application_Model_Thread 
	 */
	public function fetch($id)
	{
		//return $this->fetchRow('product_id=' . $id);
		return $this->fetchRow('id=' . $id);
	}
} /*generated by setup*/