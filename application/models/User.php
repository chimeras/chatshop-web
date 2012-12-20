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
		$lists = $this->findDependentRowset('Application_Model_ShoppingLists', 'Users', $select);
		if(isset($lists[0])){
			$list = $lists[0];
		}else{
			$list = $table->fetchNew();
			$list->setUserId($this->getId());
			$list->setType(Application_Model_ShoppingList::TYPE_UNCLASSIFIED);
			$list->setName('unclassified');
			$list->save();
		}
		return $list;
	}

	public function getShoppingPastList()
	{
		$table = new Application_Model_ShoppingLists;
		$select = $table->select()->where('`type`=?', Application_Model_ShoppingList::TYPE_PAST);
		$lists = $this->findDependentRowset('Application_Model_ShoppingLists', 'Users', $select);
		if(isset($lists[0])){
			$list = $lists[0];
		}else{
			$list = $table->fetchNew();
			$list->setUserId($this->getId());
			$list->setType(Application_Model_ShoppingList::TYPE_PAST);
			$list->setName('archived items');
			$list->save();
		}
		return $list;
	}

	public function getUnclassifiedItems()
	{
		$unclassifiedList = $this->getUnclassifiedShoppingList();
		return $unclassifiedList->getAllItemsArray();
	}


    public function getFacebook()
    {
        $table = new Application_Model_UserFacebooks;
        $fbInfo = $table->fetchUniqueBy(array('user_id'=>$this->getId()));
        if(is_object($fbInfo)){
            return new Bigbek\Facebook\User($fbInfo->getAccessToken());
        }else{
            return null;
        }
    }
}