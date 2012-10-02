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

	private $_shoplists;
	protected $errorMessage = array(
		'2001' => 'no session or user',
		'2002' => 'Shopping List doesn\'t exist'
	);

	public function __construct()
	{
		$this->_shoplists = new \Application_Model_ShoppingLists;
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
		$lists = $this->_shoplists->fetchAll('user_id=' . $this->currentUser->getId(), array('name'), $count, $offset);
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
		return \Zend_Json::encode(array('action' => 'deleted', 'id' => $list->getId()));
	}

	/**
	 * @param string $session
	 * @param string/json $params(name, privacy, state)
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
		$privacy = isset($params['state']) ? $params['state'] : \Application_Model_ShoppingList::STATE_ACTIVE;
		if ($this->currentUser == null) {
			return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
		}
		$list = $this->_shoplists->fetchNew();
		$list->setName($name);
		$list->setPrivacy($privacy);
		$list->save();
		return \Zend_Json::encode(array('action' => 'saved', 'id' => $list->getId()));
	}

}
