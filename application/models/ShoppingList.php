<?php

class Application_Model_ShoppingList extends Application_Model_Db_Row_ShoppingList
{

	const VISIBILITY_PUBLIC = 'public';
	const VISIBILITY_PRIVATE = 'private';
	const STATE_ACTIVE = 'active';
	const STATE_ARCHIVED = 'archived';

	/**
	 *
	 * @var array(Application_Model_ShoppingListItem) 
	 */
	protected $items;
	protected $itemsTable = null;

	public function __construct(array $config = array())
	{
		parent::__construct($config);
		$this->itemsTable = new Application_Model_ShoppingListItems;
	}

	/**
	 * 
	 * @return array(Application_Model_ShoppingListItems)
	 */
	public function getAllItems()
	{
		return $this->findDependentRowset('Application_Model_ShoppingListItems');
	}

	public function getAllItemsArray()
	{
		$items = array();
		foreach($this->getAllItems() as $item){
			$items[] = $item->toArray();
		}
		return $items;
	}

	/**
	 * 
	 * @return array
	 */
	public function toArray()
	{
		$return = parent::toArray();
		$return['items'] = $this->getAllItemsArray();

		return $return;
	}

	/**
	 * 
	 * @param array $item
	 * @return boolean | integer $item_id
	 */
	public function addItem($itemArray)
	{
		if(!isset($itemArray['product_id'])){
			return FALSE;
		}
		$existing = $this->itemsTable->fetchRow('`shopping_list_id`='.$this->getId() .' AND `product_id`='.(int)$itemArray['product_id']);
		if(is_object($existing)){
			return TRUE;
		}
		$item = $this->itemsTable->fetchNew();
		$item->setShoppingListId($this->getId());
		$item->setProductId($itemArray['product_id']);
		if(isset($itemArray['reminder'])){
			$item->setReminder((bool) $itemArray['reminder']);
		}
		$item->save();
		return $item->getId();
	}

}