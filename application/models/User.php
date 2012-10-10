<?php

class Application_Model_User extends Application_Model_Db_Row_User
{

	public function getShopLists()
	{
		return $this->findDependentRowset('Application_Model_ShoppingLists');
	}

	public function getUnclassifiedShoppingList()
	{
		$table = new Application_Model_ShoppingLists;
		$select = $table->select()->where('`type`=?', Application_Model_ShoppingList::TYPE_UNCLASSIFIED);
		$uLists = $this->findDependentRowset('Application_Model_ShoppingLists', 'Users', $select);
		
		if(!isset($uLists[0])){
			$uList = $table->fetchNew();
			$uList->setUserId($this->getId());
			$uList->setType(Application_Model_ShoppingList::TYPE_UNCLASSIFIED);
			$uList->setName('unclassified');
			$uList->save();
		}else{
			$uList = $uLists[0];
		}
		return $uList;
		
		
	}
	
	public function getShoppingPastList()
	{
		$table = new Application_Model_ShoppingLists;
		$select = $table->select()->where('`type`=?', Application_Model_ShoppingList::TYPE_PAST);
		return $this->findDependentRowset('Application_Model_ShoppingLists', 'Users', $select);
		
	}
	
	public function getUnclassifiedIetms()
	{
		$unclassifiedList = $this->getUnclassifiedShoppingList();
		return $unclassifiedList->getAllItemsArray();
	}

}