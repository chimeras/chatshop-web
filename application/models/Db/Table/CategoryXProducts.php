<?php
abstract class Application_Model_Db_Table_CategoryXProducts extends Zend_Db_Table_Abstract
{
	protected $_name = 'category_x_product';
	protected $_rowClass = 'Application_Model_CategoryXProduct';
//	protected $_primary = 'category_x_product_id';
	protected $_primary = 'id';
/**
	 *
	 * @param integer $id
	 * @return Application_Model_Thread 
	 */
	public function fetch($id)
	{
		//return $this->fetchRow('category_x_product_id=' . $id);
		return $this->fetchRow('id=' . $id);
	}
} /*generated by setup*/