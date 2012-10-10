<?php

namespace Bigbek\Webservice;

use Bigbek\Facebook\User as Facebook_User;

/**
 * Authentication handler class
 *
 * @author vahram
 */
class UserWebservice extends BaseWebservice
{
	/**
	 *
	 * @var type Application_Model_ShoppingLists
	 */
	private $_shoplists;
	
	/**
	 *
	 * @var type Application_Model_ShoppingListItems;
	 */
	private $_shoplistItems;
	
	protected $errorMessage = array(
		'2001' => 'no session or user',
		'2002' => 'Shopping List doesn\'t exist',
		'2003' => 'Item is not from Shopping List',
		'2004' => 'User is not shopping list owner',
		'2005' => 'Shopping list id is not specified',
		'2006' => 'Shopping list not found by specified ID'
	);

	public function __construct()
	{
		$this->_shoplists = new \Application_Model_ShoppingLists;
		$this->_shoplistItems = new \Application_Model_ShoppingListItems;
		parent::__construct();
	}

	/**
	 * @param string $session
	 * @param integer $count
	 * @param integer $offset
	 * @return string JSON
	 */
	public function getShoppingLists($session, $count = null, $offset = null)
	{
		if (!$this->setUser($session)) {
			return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
		}
		$lists = $this->_shoplists->fetchAll('`user_id`=' . $this->currentUser->getId() .' AND `state`='.  \Application_Model_ShoppingList::STATE_ACTIVE, array('name'), $count, $offset);
		return \Zend_Json::encode(array('shoplists' => $lists->toArray(), 'message' => 'successfully retreived'));
	}

	/**
	 *
	 * @param string $session 
	 * @param integer $id
	 * @return text/json (action, deleted shoplist id)
	 */
	public function deleteShoppingList($session, $id)
	{
		if (!$this->setUser($session)) {
			return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
		}
		$shoplist = $this->_shoplists->fetch($id);
		if (is_object($shoplist)) {
			$shoplist->delete();
		}
		return \Zend_Json::encode(array('action' => 'deleted'));
	}

	/**
	 * @param string $session
	 * @param string/json $shoplist(name, privacy, state, [items])
	 * @return text/json (action, new shoplist id)
	 */
	public function createShoppingList($session, $shoplist = null)
	{
		if (!$this->setUser($session)) {
			return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
		}
		$params = \Zend_Json::decode($shoplist);
		
		$name = $params['name'];
		$privacy = isset($params['privacy']) ? $params['privacy'] : \Application_Model_ShoppingList::VISIBILITY_PRIVATE;
		$state = isset($params['state']) ? $params['state'] : \Application_Model_ShoppingList::STATE_ACTIVE;
		if ($this->currentUser == null) {
			return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
		}
		
		$list = $this->_shoplists->fetchNew();
		$list->setUserId($this->currentUser->getId());
		$list->setName($name);
		$list->setPrivacy($privacy);
		$list->setState($state);
		$list->save();
		if(isset($params['items']) && is_array($params['items'])){
			foreach($params['items'] as $item){
				$list->addItem($item);
			}
		}
		return \Zend_Json::encode(array('action' => 'saved', 'id' => $list->getId()));
	}
	
	/**
	 * @param string $session
	 * @param string/json $shoplist(name, privacy, state, [items])
	 * @return text/json (action, shoplist id)
	 */
	public function modifyShoppingList($session, $shoplist = null)
	{
		if (!$this->setUser($session)) {
			return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
		}
		$params = \Zend_Json::decode($shoplist);
		if(!isset($params['id'])){
			return \Zend_Json::encode(array('error' => '2005', 'message' => $this->errorMessage['2005']));
		}
		
		$name = $params['name'];
		$privacy = isset($params['privacy']) ? $params['privacy'] : \Application_Model_ShoppingList::VISIBILITY_PRIVATE;
		$state = isset($params['state']) ? $params['state'] : \Application_Model_ShoppingList::STATE_ACTIVE;
		if ($this->currentUser == null) {
			return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
		}
		$list = $this->_shoplists->fetch($params['id']);
		if(!is_object($list)){
			return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2006']));
		}
		$list->setName($name);
		$list->setPrivacy($privacy);
		$list->setState($state);
		$list->save();
		if(isset($params['items']) && is_array($params['items'])){
			foreach($params['items'] as $item){
				$list->addItem($item);
			}
		}
		return \Zend_Json::encode(array('action' => 'saved', 'id' => $list->getId()));
	}
	
	

	
	public function getShoppingListItems($session, $id)
	{
		if (!$this->setUser($session)) {
			return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
		}
		
		$shoppingList = $this->_shoplists->fetch($id);
		return $shoppingList->getAllItemsArray();
	}
	
	
	public function getUnclassifiedItems($session)
	{
		if (!$this->setUser($session)) {
			return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
		}
		
		\Zend_Json::encode(array('list' => $this->currentUser->getUnclassifiedIetms()));
		return $shoppingList->getAllItemsArray();
	}
	
	/**
	 * 
	 * @param string $session
	 * @param int $shoppingListId
	 * @param string/json $item
	 * @return int
	 */
	public function addShoppingListItem($session, $shoppingListId, $item)
	{
		if (!$this->setUser($session)) {
			return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
		}
		$shoppingList = $this->_shoplists->fetch($shoppingListId);
		return $shoppingList->addItem(\Zend_Json::decode($item));
	}
	
	
	public function archiveItem($session, $id)
	{
		if (!$this->setUser($session)) {
			return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
		}
		$item = $this->_shoplistItems->fetch($id);
		$shoppingList = $item->getShopList();
		if($shoppingList->getUser()->getId() == $this->currentUser->getId()){
			$pastItemsList = $this->currentUser->getShoppingPastList();
			$item->setShoppingListId($pastItemsList->getId());
		}
		
	}
	
	
	/**
	 * 
	 * @param string $session
	 * @param int $shoppingListId
	 * @param int $itemId
	 * @return bool
	 */
	public function deleteShoppingListItem($session, $shoppingListId, $itemId)
	{
		if (!$this->setUser($session)) {
			return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
		}
		$shoppingList = $this->_shoplists->fetch($shoppingListId);
		if($shoppingList->getUserId() != $this->currentUser->getId()){
			return \Zend_Json::encode(array('error' => '2004', 'message' => $this->errorMessage['2004']));
		} 
		$shoppingListItem = $this->_shoplistItems->fetch($shoppingListId);
		if($shoppingListItem->getShopList() == $shoppingList){
			$shoppingListItem->delete();
			return \Zend_Json::encode(array('action' => 'removed'));
		}else{
			return \Zend_Json::encode(array('error' => '2003', 'message' => $this->errorMessage['2003']));
		}
		
	}
}
