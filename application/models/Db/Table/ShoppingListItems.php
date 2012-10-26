<?php
abstract class Application_Model_Db_Table_ShoppingListItems extends Zend_Db_Table_Abstract
{
	protected $_name = 'shopping_list_item';
	protected $_rowClass = 'Application_Model_ShoppingListItem';
//	protected $_primary = 'shopping_list_item_id';
	protected $_primary = 'id';
/**
	 *
	 * @param integer $id
	 * @return Application_Model_ShoppingListItem 
	 */
	public function fetch($id)
	{
		//return $this->fetchRow('shopping_list_item_id=' . $id);
		return $this->fetchRow('id=' . $id);
	}
} /*generated by setup*/