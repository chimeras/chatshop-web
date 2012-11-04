<?php

class Application_Model_AdvertiserCategories extends Application_Model_Db_Table_AdvertiserCategories
{
	public $uniqueFields = array('name');
	protected $_referenceMap = array(
		'Category' => array(
			'columns' => array('category_id'), /* foreign key */
			'refTableClass' => 'Application_Model_Categories',
			'refColumns' => array('id') /* primary key of parent table */
		)
	);

	public function fetchAllArray()
	{
		$objects = $this->fetchAll();
		$array = array();
		foreach ($objects as $object) {
			$array[] = $object->toArray();
		}
		return $array;
	}

}