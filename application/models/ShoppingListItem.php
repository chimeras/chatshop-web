<?php
class Application_Model_ShoppingListItem extends Application_Model_Db_Row_ShoppingListItem
{

	/**
	 * 
	 * @return Application_Model_ShoppingLists;
	 */
	public function getShopList()
	{
		return $this->findParentRow('Application_Model_ShoppingLists');
	}
}