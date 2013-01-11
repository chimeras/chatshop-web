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
        '2006' => 'Shopping list not found by specified ID',
        '2007' => 'Item owner is not the specified user',
        '2008' => 'Reminder cannot be saved please check if all mandatory fields are filled',
        '2009' => 'Shopping list Id is not seem to be correct',
        '2010' => 'There is no product matching this shopping list item',
        '2011' => 'You have already this shopping list',
        '2012' => 'Product does not exist',
    );

    public function __construct()
    {
        $this->_shoplists = new \Application_Model_ShoppingLists;
        $this->_shoplistItems = new \Application_Model_ShoppingListItems;
        parent::__construct();
    }

    // [{"name":"cherchezlafemme","privacy":1,"items":[{"product_id":326454, "reminder":1}, {"product_id":326522, "reminder":1}], "timestamp":1361541103}, { "name":"bulkinafasion","privacy":1, "state":1, "type":1, "items":[{"product_id":326507, "reminder":1}, {"product_id":326480, "reminder":1}], "timestamp":1361541103}]
    public function syncShoppingLists($session, $lists)
    {
        if (!$this->setUser($session)) {
            return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
        }
        try {
            $lists = \Zend_Json::decode($lists);
        } catch (\Exception $e) {
            return \Zend_Json::encode(array('error' => '3000', 'message' => $e->getMessage()));
        }
        $table = new \Application_Model_ShoppingLists;
        foreach ($lists as $shoppingListArray) {
            if (!isset($shoppingListArray['id'])) {
                $shoppingList = $table->fetchUniqueBy(array(
                    'name' => $shoppingListArray['name'],
                    'user_id' => $this->currentUser->getId()));
                if (!is_object($shoppingList)) {
                    $shoppingList = $table->fetchNew();
                    $shoppingList->setUserId($this->currentUser->getId());
                    $shoppingList->save();
                }

            } else {
                $shoppingList = $table->fetch($shoppingListArray['id']);
                if (!is_object($shoppingList)) {
                    return \Zend_Json::encode(array('error' => '3011', 'message' => 'no shopping list with provided id'));
                }
                if($shoppingList->getUserId() != $this->currentUser->getId()){
                    return \Zend_Json::encode(array('error' => '3012', 'message' => 'user is not owner of this list'));
                }
            }
            if (!isset($shoppingListArray['timestamp']) || strtotime($shoppingList->getModified()) <= $shoppingListArray['timestamp']) {
                $shoppingList->setModified(date('Y-m-d H:i:s'));
                if(isset($shoppingListArray['name'])){
                    $shoppingList->setName($shoppingListArray['name']);
                }
                if(isset($shoppingListArray['privacy'])){
                    $shoppingList->setPrivacy($shoppingListArray['privacy']);
                }
                if(isset($shoppingListArray['type'])){
                    $shoppingList->setState($shoppingListArray['type']);
                }
                if(isset($shoppingListArray['state'])){
                    $shoppingList->setState($shoppingListArray['state']);
                }
                $shoppingList->save();
                $shoppingList->deleteAllItems();
                foreach ($shoppingListArray['items'] as $item) {
                    $shoppingList->addItem($item);
                }
            }

        }


        $lists = $this->_shoplists->fetchAll('`user_id`=' . $this->currentUser->getId());
        $result = array();
        foreach($lists as $list){
            $member = $list->toArray();
            $member['items'] = $list->getAllItemsArray(false);
            $result[] = $member;
        }

        return \Zend_Json::encode(array('shoplists' => $result, 'message' => 'successfully synchronized'));
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
        $lists = $this->_shoplists->fetchAll('`user_id`=' . $this->currentUser->getId() . ' AND `state`=' . \Application_Model_ShoppingList::STATE_ACTIVE, array('name'), $count, $offset);
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
        try {
            $params = \Zend_Json::decode($shoplist);
        } catch (\Exception $e) {
            return \Zend_Json::encode(array('error' => '3000', 'message' => $e->getMessage()));
        }


        $name = $params['name'];
        $privacy = isset($params['privacy']) ? $params['privacy'] : \Application_Model_ShoppingList::VISIBILITY_PRIVATE;
        $state = isset($params['state']) ? $params['state'] : \Application_Model_ShoppingList::STATE_ACTIVE;
        if ($this->currentUser == null) {
            return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
        }
        $existing = $this->_shoplists->fetchUniqueBy(array('user_id' => $this->currentUser->getId(),
            'name' => $name));

        if (is_object($existing)) {
            return \Zend_Json::encode(array('error' => '2011', 'message' => $this->errorMessage['2011']));
        }

        $list = $this->_shoplists->fetchNew();
        $list->setUserId($this->currentUser->getId());
        $list->setName($name);
        $list->setPrivacy($privacy);
        $list->setState($state);
        $list->save();
        $productTable = new \Application_Model_Products;

        if (isset($params['items']) && is_array($params['items'])) {
            foreach ($params['items'] as $item) {
                $product = $productTable->fetch($item['product_id']);
                if (!is_object($product)) {
                    return \Zend_Json::encode(array('error' => '2012', 'message' => $this->errorMessage['2012'], 'product_id' => $item['product_id']));
                }
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
        if (!isset($params['id'])) {
            return \Zend_Json::encode(array('error' => '2005', 'message' => $this->errorMessage['2005']));
        }

        $name = $params['name'];
        $privacy = isset($params['privacy']) ? $params['privacy'] : \Application_Model_ShoppingList::VISIBILITY_PRIVATE;
        $state = isset($params['state']) ? $params['state'] : \Application_Model_ShoppingList::STATE_ACTIVE;
        if ($this->currentUser == null) {
            return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
        }
        $list = $this->_shoplists->fetch($params['id']);
        if (!is_object($list)) {
            return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2006']));
        }
        $list->setName($name);
        $list->setPrivacy($privacy);
        $list->setState($state);
        $list->save();
        if (isset($params['items']) && is_array($params['items'])) {
            foreach ($params['items'] as $item) {
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
        return \Zend_Json::encode(array('list' => $this->currentUser->getUnclassifiedIetms()));
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
        if ($shoppingList->getUser()->getId() == $this->currentUser->getId()) {
            $pastItemsList = $this->currentUser->getShoppingPastList();
            $item->setShoppingListId($pastItemsList->getId());
            $item->save();
            return \Zend_Json::encode(array('action' => 'archived'));
        } else {
            return \Zend_Json::encode(array('error' => '2007', 'message' => $this->errorMessage['2007']));
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
        if ($shoppingList->getUserId() != $this->currentUser->getId()) {
            return \Zend_Json::encode(array('error' => '2004', 'message' => $this->errorMessage['2004']));
        }
        $shoppingListItem = $this->_shoplistItems->fetch($itemId);
        if ($shoppingListItem->getShopList() == $shoppingList) {
            $shoppingListItem->delete();
            return \Zend_Json::encode(array('action' => 'removed'));
        } else {
            return \Zend_Json::encode(array('error' => '2003', 'message' => $this->errorMessage['2003']));
        }
    }

    public function addReminder($session, $reminder)
    {
        $logger = \Zend_Registry::get('logger');
        $logger->log('addReminder:' . substr($reminder, 0, 100), \Zend_Log::DEBUG);
        if (!$this->setUser($session)) {
            return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
        }
        $remindersTable = new \Application_Model_Reminders;
        $Reminder = $remindersTable->fetchNew();
        $data = \Zend_Json::decode($reminder);
        $result = $Reminder->fillFrom($data);
        if ($result === TRUE) {
            $Reminder->save();
            if (isset($data['imagedata'])) {
                $uploadManager = new \Application_Model_Uploads;


                $image = $uploadManager->addImage($Reminder->getId(), $data['imagedata']);
                if (is_string($image)) {
                    $Reminder->setImageUrl($image);
                    $Reminder->save();
                }
            }

            return \Zend_Json::encode(array('action' => 'added', 'id' => $Reminder->getId()));
        } else {
            return \Zend_Json::encode(array('error' => '2008', 'message' => $this->errorMessage['2008']));
        }
    }


    /**
     * @param string $session
     * @param int $offset default(0)
     * @param int $limit default(0)
     * @return string
     */
    public function getFriendsList($session, $offset = 0, $limit = 0)
    {
        $logger = \Zend_Registry::get('logger');
        if (!$this->setUser($session)) {
            return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
        }
        $fb = $this->currentUser->getFacebook();
        return \Zend_Json::encode(array('friends' => $fb->getFriends($offset, $limit)));
    }


    /**
     * @param string $session
     * @param integer $shoppingListItemId
     * @return string
     */
    public function fbShareShoppingListItem($session, $shoppingListItemId)
    {

        $logger = \Zend_Registry::get('logger');
        if (!$this->setUser($session)) {
            return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
        }

        $table = new \Application_Model_ShoppingListItems;
        $item = $table->fetch((int)$shoppingListItemId);
        if (!is_object($item) || $item->getShopList()->getUserId() !== $this->currentUser->getId()) {
            return \Zend_Json::encode(array('error' => '2009', 'message' => $this->errorMessage['2009']));
        }
        $tableP = new \Application_Model_Products;
        $product = $tableP->fetch($item->getProductId());
        if (!is_object($product)) {
            return \Zend_Json::encode(array('error' => '2010', 'message' => $this->errorMessage['2010']));
        }

        $fb = $this->currentUser->getFacebook();
        return \Zend_Json::encode(array('share_id' => $fb->sharePost('I like ' . $product->getName(), $product->getImageUrl(), $product->getBuyUrl(), $product->getName())));
    }


    /**
     * @param string $session
     * @param integer $productId
     * @return string
     */
    public function fbShareProduct($session, $productId)
    {

        $logger = \Zend_Registry::get('logger');
        $logger->log('fbShareProduct($session, $productId):' . $session . ',' . $productId, \Zend_Log::DEBUG);
        if (!$this->setUser($session)) {
            return \Zend_Json::encode(array('error' => '2001', 'message' => $this->errorMessage['2001']));
        }
        $tableP = new \Application_Model_Products;
        $product = $tableP->fetch($productId);
        if (!is_object($product)) {
            return \Zend_Json::encode(array('error' => '2010', 'message' => $this->errorMessage['2010']));
        }

        $fb = $this->currentUser->getFacebook();
        $result = \Zend_Json::encode(array(
            'share_id' => $fb->sharePost('I like ' . $product->getName(),
                $product->getImageUrl(),
                $product->getBuyUrl(),
                $product->getName())));
        $logger->log('fbShareProduct:params=p-id=' . $productId . ',result=' . $result, \Zend_Log::DEBUG);
        return $result;
    }
}
