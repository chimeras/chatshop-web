<?php

class Application_Model_Theme extends Application_Model_Db_Row_Theme
{

	public $categories = array();
	public function __construct(array $config = array())
	{
		parent::__construct($config);
		$this->categories = $this->_fetchCategories();
	}

	private function _fetchCategories()
	{
		return $this->findManyToManyRowset('Application_Model_Categories', 'Application_Model_ThemeXCategories');
	}

	public function getCategories()
	{
		return $this->categories;
	}
	
	
	public function toArray()
	{
		$array = parent::toArray();
		$array['categories'] = $this->getCategories()->toArray();
		return $array;
	}
	
	
	
	public function getCategoriesArray()
	{
		$categoryTable = new Application_Model_AdvertiserCategories;
		$Categories = $categoryTable->fetchAll();
		$return = array();
		foreach($Categories as $Category){
			$categoryArray = $Category->toArray();
			$categoryArray['products'] = $Category->getProductsArray();
			$return[] = $categoryArray;
		}
		return $return;
	}
}