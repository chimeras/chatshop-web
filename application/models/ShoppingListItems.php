<?php
class Application_Model_ShoppingListItems extends Application_Model_Db_Table_ShoppingListItems
{
	protected $_referenceMap = array(
		'ShoppingList' => array(
			'columns' => array('shopping_list_id'), /*foreign key*/
			'refTableClass' => 'Application_Model_ShoppingLists',
			'refColumns' => array('id') /*primary key of parent table*/
		));
}