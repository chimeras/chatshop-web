<?php

class Application_Model_AdvertiserCategory extends Application_Model_Db_Row_AdvertiserCategory
{
	/*public function getProducts($count = 20, $offset = 0)
	{
		$table = new Application_Model_Products;
		$select = $table->select()->limit($count, $offset);
		return $this->findDependentRowset('Application_Model_Products', 'AdvertiserCategory', $select);
	}
	
	public function getProductsArray($count = 20, $offset = 0){
		$Products = $this->getProducts($count, $offset);
		$products = array();
		foreach($Products as $Product){
			$productArray = $Product->toArray();
			$productArray['similar_items_count'] = $Product->getSimilarItemsCount();
			$products[] = $productArray;
		}
		return $products;
	}
*/
}