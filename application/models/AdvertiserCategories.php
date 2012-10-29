<?php
class Application_Model_AdvertiserCategories extends Application_Model_Db_Table_AdvertiserCategories
{
	public $uniqueFields = array('name');

	public function fetchAllArray()
	{
		$objects = $this->fetchAll();
		$array = array();
		foreach($objects as $object){
			$array[] = $object->toArray();
		}
		return $array;
	}
	
}