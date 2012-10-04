<?php
class Application_Model_CategoryXProducts extends Application_Model_Db_Table_CategoryXProducts
{
	protected $_referenceMap = array(
		'Category' => array(
			'columns' => array('category_id'), /*foreign key*/
			'refTableClass' => 'Application_Model_Categories',
			'refColumns' => array('id') /*primary key of parent table*/
		),
			'Product' => array(
			'columns' => array('product_id'), /*foreign key*/
			'refTableClass' => 'Application_Model_Products',
			'refColumns' => array('id') /*primary key of parent table*/
		)
	);
	
}