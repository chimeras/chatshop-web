<?php
class Application_Model_AdvertiserCategory extends Application_Model_Db_Row_AdvertiserCategory
{

	public function getProductsArray()
	{
		$return = array();
		foreach($this->getProducts() as $Product){
			$return[] = $Product->toArray();
		}
		return $return;
	}
	
	public function getProducts()
	{
		$table = new Application_Model_Products;
		$select = $table->select()->limit(20);
		return $this->findDependentRowset('Application_Model_Products', 'AdvertiserCategory', $select);
	}
}