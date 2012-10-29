<?php

class Application_Model_Brands extends Application_Model_Db_Table_Brands
{

	public $uniqueFields = array('name');


	public function getAllArray()
	{
		$entities = $this->fetchAll();
		$result = array();
		foreach ($entities as $entity) {
			$result[$entity->getId()] = $entity->getName();
		}
		return $result;
	}

	
}