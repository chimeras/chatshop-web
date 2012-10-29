<?php
class Application_Model_Retailers extends Application_Model_Db_Table_Retailers
{
	public $uniqueFields = array('name');
	public function getAllArray()
	{
		$entities = $this->fetchAll();
		$result = array();
		foreach ($entities as $entity){
			$result[$entity->getId()] = $entity->getName();
		}
		return $result;
	}
}