<?php

class Application_Model_ShoppingList extends Application_Model_Db_Row_ShoppingList
{

	const VISIBILITY_PUBLIC = 1;
	const VISIBILITY_PRIVATE = 2;
	const STATE_ACTIVE = 1;
	const STATE_ARCHIVED = 2;
	const TYPE_NORMAL = 1;
	const TYPE_UNCLASSIFIED = 2;
	const TYPE_PAST = 3;

    const ACTION_DELETE = 0;
    const ACTION_CREATE = 1;
    const ACTION_UPDATE = 2;
    const ACTION_SKIP = 3;
    const ACTION_OVERRIDE = 4;


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

	public function getAllItemsArray($fullProductInfo = true)
	{
        $productsTable = new Application_Model_Products;

		$items = array();
		foreach($this->getAllItems() as $item){
            if($fullProductInfo){
                $product = $productsTable->fetch($item->getProductId());
                $items[] = array('product'=>$product->toArray(), 'reminder'=>$item->getReminder());
            }else{
                $items[] = array('product_id'=>$item->getProductId(), 'reminder'=>$item->getReminder());
            }

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
		$return['timestamp'] = strtotime($this->getModified());

		return $return;
	}

	/**
	 * 
	 * @param array $item
	 * @return boolean | integer $item_id
	 */
	public function addItem($itemArray)
	{
		if(!isset($itemArray['product_id'])
            && isset($itemArray['reminder'], $itemArray['name'])
            && $itemArray['reminder']){ // reminder case
            $table = new Application_Model_Reminders;
            $select = $table->select()
            ->where('`shopping_list_id`=?'.$this->getId())
            ->where('name LIKE?', $itemArray['name']);
            $existings = $table->fetchAll($select);
            if(count($existings) > 0){
                $item = $existings[0];
            }else{
                $item = $table->createRow();
                $item->setShoplistId();
                $item->setName($itemArray['name']);
            }
            if(isset($itemArray['description'])){
                $item->setDescription($itemArray['description']);
            }
            $item->save();
		}else{ // product case
            $existing = $this->itemsTable->fetchRow('`shopping_list_id`='.$this->getId() .' AND `product_id`='.(int)$itemArray['product_id']);
            if(is_object($existing)){
                return true;
            }
            $item = $this->itemsTable->createRow();
            $item->setShoppingListId($this->getId());
            $item->setProductId($itemArray['product_id']);
            $item->save();
            return $item->getId();
        }
	}


	public function getUser()
	{
		return $this->findParentRow('Application_Model_Users');
	}

    public function deleteAllItems()
    {
        $table = new Application_Model_ShoppingListItems;
        $table->delete("shopping_list_id=".$this->getId());
    }
}