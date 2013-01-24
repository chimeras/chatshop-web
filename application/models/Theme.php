<?php

class Application_Model_Theme extends Application_Model_Db_Row_Theme
{

	public $categories = array();

	public function __construct(array $config = array())
	{
		parent::__construct($config);
		$this->categories = $this->_fetchCategories(true);
	}

	private function _fetchCategories($includeSubcategories)
	{
		$Categories = $this->findManyToManyRowset('Application_Model_Categories', 'Application_Model_ThemeXCategories');
		if ($includeSubcategories) {
			$return = array();
			foreach ($Categories as $Category) {
				$return[] = $Category;
				foreach ($Category->getSubcategories() as $sub) {
					$return[] = $sub;
				}
			}
		} else {
			$return = $Categories;
		}
		return $return;
	}

	public function getCategories()
	{
		return $this->categories;
	}

	public function getCategoriesArray()
	{
		$return = array();
		foreach ($this->_fetchCategories(false) as $Category) {
            foreach($Category->getSubcategories() as $sub){
                $return[] = $sub->toCombinedArray(20, 0, true);
            }

		}
		return $return;
	}

	public function toCombinedArray()
	{
		$array = parent::toArray();
		$array['categories'] = $this->getCategories()->toArray();
		return $array;
	}

	public function getAdvertiserCategoriesArray()
	{
		$categoryTable = new Application_Model_AdvertiserCategories;
		$Categories = $categoryTable->fetchAll();
		$return = array();
		foreach ($Categories as $Category) {
			$categoryArray = $Category->toArray();
			$categoryArray['products'] = $Category->getProductsArray();
			$return[] = $categoryArray;
		}
		return $return;
	}

}