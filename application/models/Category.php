<?php

class Application_Model_Category extends Application_Model_Db_Row_Category
{

	public $products = array();

	/**
	 * 
	 * @return array(Application_Model_Products)
	 */
	public function getProducts()
	{
		return $this->findManyToManyRowset('Application_Model_Products', 'Application_Model_CategoryXProducts');
	}

	/**
	 * 
	 * @return array()
	 */
	public function getProductsArray()
	{
		$products = array();
		foreach($this->getProducts() as $product){
			$products[] = $product->toArray();
		}
		return $products;
	}

}