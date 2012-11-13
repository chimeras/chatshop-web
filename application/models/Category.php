<?php

class Application_Model_Category extends Application_Model_Db_Row_Category
{

	public $products = array();
	private $_subcategories = null;
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
			$prod = $product->toArray();
			$prod['parent_category_id'] = $this->getId();
			$products[] = $prod;
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
			$productArray['parent_category_id'] = $Product->parent_category_id;
			$productArray['similar_items_count'] = $Product->getSimilarItemsCount();
			$category['products'][] = $productArray;
		}
		$category['subcategories'] = $this->getSubcategoriesArray();
		$category['products_qty'] = $this->getProductsCount();
		return $category;
	}

	public function getSubcategoriesArray()
	{
		if($this->_subcategories == null){
			$subs = array();
			foreach ($this->getSubcategories() as $Sub){
				$subs[] = array('id'=>$Sub->getId(),
					'name' => $Sub->getName(), 
					'products_qty'=>$Sub->getProductsCount());
			}
			$this->_subcategories = $subs;
		}
		return $this->_subcategories;
	}
	public function getProducts($rowCount, $page)
	{
		$ids = array();
		$subIds = array();
		foreach($this->getAdvertiserCategories() as $cACategory){
			$ids[] = $cACategory->getId();
			$subIds[$cACategory->getId()] = $this->getId();
		}
		
		foreach ($this->getSubcategories() as $sub){
			foreach($sub->getAdvertiserCategories() as $subACategory){
				$ids[] = $subACategory->getId();
				$subIds[$subACategory->getId()] = $sub->getId();
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
		$results = array();
		foreach($table->fetchAll($select) as $Product){
			$Product->parent_category_id = $subIds[$Product->getAdvertiserCategoryId()];
			$results[] = $Product;
		}
		return $results;
	}

	
	public function getProductsCount()
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
			return 0;
		}
		$table = new Application_Model_Products;
		$select = $table->select('*')
				->group('similarity')
				->where('`advertiser_category_id` IN('. implode(',', $ids) .')');
		return $table->fetchAll($select)->count();
	}

	
}