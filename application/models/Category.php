<?php

class Application_Model_Category extends Application_Model_Db_Row_Category
{

	public $products = array();

	/**
	 * 
	 * @return array(Application_Model_AdvertiserCategory)
	 */
	public function getAdvertiserCategories()
	{
		return $this->findDependentRowset('Application_Model_AdvertiserCategories');
	}

	/**
	 * 
	 * @return array()
	 */
	public function getProductsArray()
	{
		$products = array();
		foreach ($this->getProducts() as $product) {
			$products[] = $product->toArray();
		}
		return $products;
	}

	public function getSubcategories()
	{
		return $this->findDependentRowset('Application_Model_Categories');
	}

	public function toCombinedArray($productsCount = 20)
	{
		$category = $this->toArray();
		$category['products'] = array();
		foreach ($this->getProducts($productsCount, 0) as $product){
			$category['products'][] = $product->toArray();
		}
		/*if (is_numeric($productsCount)) {
			$AdvertiserCategories = $this->getAdvertiserCategories();
			$advCount = count($AdvertiserCategories);
			if ($advCount > 0) {
				$productsCount = ceil(20 / $advCount);
			} else {
				$productsCount = 0;
			}
			$category['products'] = array();
			foreach ($AdvertiserCategories as $AdvertiserCategory) {
				$category['products'] = array_merge($category['products'], $AdvertiserCategory->getProductsArray($productsCount));
			}
		}*/

		return $category;
	}

	public function getProducts($count, $offset)
	{

		$ids = array();
		foreach($this->getAdvertiserCategories() as $cACategory){
			$ids[] = $cACategory->getId();
		}
		
		foreach ($this->getSubcategories() as $sub){
			foreach($sub->getAdvertiserCategories() as $subACategory){
				$ids[] = $subACategory->getId();
			}
			
		}
		if(count($ids) == 0){
			return array();
		}
		$table = new Application_Model_Products;
		return $table->fetchAll('`advertiser_category_id` IN('. implode(',', $ids) .')', null, $count, $offset);
		
	}

	
}