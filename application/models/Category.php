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

	public function toCombinedArray($productsCount = null)
	{
		$category = $this->toArray();

		if (is_numeric($productsCount)) {
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
		}

		return $category;
	}

	public function getProducts($count, $offset)
	{

		$table = new Application_Model_Products;
		$select = $table->select('*');
		$select->setIntegrityCheck(false);
		$select->join('advertiser_category', 'advertiser_category_id = advertiser_category.id')
				->where('`advertiser_category`.`category_id`=?', $this->getId())
				->limit($count, $offset);

		return $this->findDependentRowset('Application_Model_Products', 'Category', $select);
	}

}