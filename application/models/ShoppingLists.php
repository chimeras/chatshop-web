<?php

class Application_Model_ShoppingLists extends Application_Model_Db_Table_ShoppingLists
{

	//protected $_dependentTables = 'Application_Model_ShoppingListItem';
	protected $_referenceMap = array(
		'Users' => array(
			'columns' => array('user_id'), /* foreign key */
			'refTableClass' => 'Application_Model_Users',
			'refColumns' => array('id') /* primary key of parent table */
		)
	);

    private $_mandatoryFields = array('id', 'items', 'name', 'privacy', 'state');

	public function __construct($config = array())
	{
		parent::__construct($config);
	}


    /**
     * //[{"name":"da vinci", "privacy":"1", "state":"1", "items":[{"product_id":6115, "reminder":0}], "syncstatus":1}]
     * @param array(Application_Model_ShoppingList) $lists
     * @param Application_Model_User $user
     * @return array
     */
    public function synchronise($lists, Application_Model_User $user)
    {
        $results = array();
        $objects = array();
        foreach($this->fetchAll("user_id = ". $user->getId()) as $object){
            $objects[$object->getId()] = $object;
            $results[$object->getId()] = $object->toArray();
        }
        foreach($lists as $list){
            if(!isset($list['syncstatus'])){
                continue;
            }
            switch ($list['syncstatus'])
            {
                case 0: // delete
                    if(isset($list['id'])){
                        $item = $objects[$list['id']];
                        if(is_object($item)){
                            unset($objects[$item->getId()]);
                            $item->delete();
                        }
                    }
                    break;
                case 1: // create
                    if($this->_check($list) == array('id')){
                        $existing = $this->_getExisting($list['name'], $user->getId());
                        if(is_object($existing)){
                            $arrShoppingList = $list;
                            $arrShoppingList['conflicted_with'] = $existing->toArray();
                            $results[$existing->getId()] = $arrShoppingList;
                            break;
                        }
                        $shoppingList = $this->createRow();
                        foreach($this->_mandatoryFields as $field){
                            if(in_array($field, array('id', 'items'))){
                                continue;
                            }

                            $setterName = 'set'. ucfirst($field);
                            $shoppingList->$setterName($list[$field]);
                        }
                        $shoppingList->setUserId($user->getId());
                        $shoppingList->setModified(date('Y-m-d H:i:s'));
                        $shoppingList->save();
                        $this->_setItemsFromArray($shoppingList, $list['items']);
                        $results[$shoppingList->getId()] = $shoppingList->toArray();
                    }

                    break;
                case 2: // update

                    if($this->_check($list) == array()){
                        $shoppingList = $objects[$list['id']];
                        if(!is_object($shoppingList)){
                            break;
                        }
                        if(strtotime($shoppingList->getModified()) > $list['timestamp']){
                            $arrShoppingList = $list;
                            $arrShoppingList['conflicted_with'] = $shoppingList->toArray();
                            $results[$shoppingList->getId()] = $arrShoppingList;

                        }else{
                            foreach($this->_mandatoryFields as $field){
                                if(in_array($field, array('id', 'items'))){
                                    continue;
                                }

                                $setterName = 'set'. ucfirst($field);
                                $shoppingList->$setterName($list[$field]);
                            }
                            $shoppingList->setModified(date('Y-m-d H:i:s'));
                            $shoppingList->save();
                            $this->_setItemsFromArray($shoppingList, $list['items']);
                            $results[$shoppingList->getId()] = $shoppingList->toArray();
                        }

                    }


                    break;
                case 4:   // overriding

                    if($this->_check($list) === array()){
                        $shoppingList = $objects[$list['id']];
                        if(!is_object($shoppingList)){
                            break;
                        }
                        foreach($this->_mandatoryFields as $field){
                            if(in_array($field, array('id', 'items'))){
                                continue;
                            }
                            $setterName = 'set'. ucfirst($field);
                            $shoppingList->$setterName($list[$field]);
                        }
                        $shoppingList->setModified(date('Y-m-d H:i:s'));
                        $shoppingList->save();
                        $this->_setItemsFromArray($shoppingList, $list['items']);
                        $results[$shoppingList->getId()] = $shoppingList->toArray();
                    }
                    break;
                case 3:   // not modified skipping
                default:    // not modified skipping
            }
        }
        return $results;
    }

    private function _check($array)
    {
        $error = array();
        foreach($this->_mandatoryFields as $mandatory){
            if(!isset($array[$mandatory])){
                array_push($error, $mandatory);
            }
        }
        return $error;
    }

    /**
     * @param Application_Model_ShoppingList $shoppingList
     * @param array() $items
     * @return bool | array(error)
     */
    private function _setItemsFromArray(Application_Model_ShoppingList $shoppingList, $items)
    {

        foreach($items as $item){
            if($shoppingList->addItem($item) === false){
                return array('error_message'=>print_r($item, 1));
            }
        }
        return true;
    }

    /**
     * @param string $name
     * @param integer $userId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    private function _getExisting($name, $userId)
    {
        return $this->fetchUniqueBy(array('user_id'=>$userId, 'name'=>$name));
    }
}