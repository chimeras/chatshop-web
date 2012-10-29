<?php

class Application_Model_Products extends Application_Model_Db_Table_Products
{

	protected $_referenceMap = array(
		'Brand' => array(
			'columns' => array('brand_id'), /* foreign key */
			'refTableClass' => 'Application_Model_Brands',
			'refColumns' => array('id') /* primary key of parent table */
		),
		'Retailer' => array(
			'columns' => array('retailer_id'), /* foreign key */
			'refTableClass' => 'Application_Model_Retailers',
			'refColumns' => array('id') /* primary key of parent table */
		),
		'AdvertiserCategory' => array(
			'columns' => array('advertiser_category_id'), /* foreign key */
			'refTableClass' => 'Application_Model_AdvertiserCategories',
			'refColumns' => array('id') /* primary key of parent table */
		)
	);
	
	
	
	public function fetchAllArray()
	{
		$objects = $this->fetchAll();
		$array = array();
		foreach($objects as $object){
			$array[] = $object->toArray();
		}
	}
}