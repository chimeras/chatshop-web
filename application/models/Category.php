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

	public function toCombinedArray($productsCount = 20, $offset = 0)
	{
		$category = $this->toArray();
		$category['products'] = array();
		foreach ($this->getProducts($productsCount, $offset) as $Product){
			$productArray = $Product->toArray();
			$productArray['similar_items_count'] = $Product->getSimilarItemsCount();
			$category['products'][] = $productArray;
		}
		$category['products_qty'] = count($category['products']);
		return $category;
	}

	public function getProducts($rowCount, $page)
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
		$select = $table->select('*')
				->group('similarity')
				->where('`advertiser_category_id` IN('. implode(',', $ids) .')')
				->limitPage($page, $rowCount);
		return $table->fetchAll($select);
	}

	
}