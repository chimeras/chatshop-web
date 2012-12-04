<?php

class Application_Model_Category extends Application_Model_Db_Row_Category
{

	public $products = array();
	private $_subcategories = null;
	private $_parentIds = array();

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
		foreach ($this->getProducts(1000, 1) as $product) {
			$prod = $product->toArray();
		//	$prod['parent_category_id'] = $this->getId();
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
		foreach ($this->getProducts($productsCount, $offset) as $Product) {
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
		if ($this->_subcategories == null) {
			$subs = array();
			foreach ($this->getSubcategories() as $Sub) {
				$subs[] = array('id' => $Sub->getId(),
					'name' => $Sub->getName(),
					'products_qty' => $Sub->getProductsCount());
			}
			$this->_subcategories = $subs;
		}
		return $this->_subcategories;
	}

	public function getProducts($rowCount, $page)
	{
		/*$ids = array();*/
		$retailersIds = $this->_getRetailersIds();
		$specificRetailersIds = $this->_getSpecificRetailersIds();
		$subIds = array();
        $keywords = array();
        $mandatoryKeywords = array();
        if(is_object($this->getParent())){
            $mandatoryKeywords[] = $this->getParent()->getName();
        }
        $mandatoryKeywords[] = $this->getName();

		foreach ($this->getSubcategories() as $sub) {
            $keywords[] = $sub->getName();
		}
		if (count($retailersIds)==0) {
			return array();
		}


		$table = new Application_Model_Products;
        $keywordCondition = $table->select();
        foreach($keywords as $keyword){
            $keywordCondition->orWhere("`keywords` LIKE ?", '% '.$keyword.'%');
            $keywordCondition->orWhere("`keywords` LIKE ?", $keyword.'%');
        }
        if(count($keywords) == 0){
            $keywordCondition->orWhere("true");
        }
        $mandatoryKeywordCondition = $table->select();
        $mandatoryKeywordInverseCondition = $table->select();
        foreach($mandatoryKeywords as $keyword){
            $mandatoryKeywordCondition->orWhere("`keywords` LIKE ?", '% '.$keyword.'%');
            $mandatoryKeywordCondition->orWhere("`keywords` LIKE ?", $keyword.'%');
            $mandatoryKeywordInverseCondition->Where("`keywords` NOT LIKE ?", '% '.$keyword.'%');
            $mandatoryKeywordInverseCondition->Where("`keywords` NOT LIKE ?", $keyword.'%');
        }
        $select = $table->select('*')
            ->group('similarity')
            ->where('`visible`=?', Application_Model_Product::VISIBILITY_VISIBLE)
            ->where('`retailer_id` IN(' . implode(',', $specificRetailersIds) . ')')
            ->where(implode (' ', $mandatoryKeywordInverseCondition->getPart(Zend_Db_Select::WHERE)))
            ->where(implode (' ', $keywordCondition->getPart(Zend_Db_Select::WHERE)))
            ->limitPage($page, $rowCount);
        //echo $select; exit();
        $results = array();
        foreach ($table->fetchAll($select) as $Product) {
            $Product->parent_category_id = $this->_getParentCategoryId($Product->getAdvertiserCategoryId());
            $results[] = $Product;
        }

        $select = $table->select('*')
				->group('similarity')
				->where('`visible`=?', Application_Model_Product::VISIBILITY_VISIBLE)
				->where('`retailer_id` IN(' . implode(',', $retailersIds) . ')')
				->where(implode (' ', $mandatoryKeywordCondition->getPart(Zend_Db_Select::WHERE)))
				->where(implode (' ', $keywordCondition->getPart(Zend_Db_Select::WHERE)))
				->limitPage($page, $rowCount);
        //echo $select; exit();
		foreach ($table->fetchAll($select) as $Product) {
			$Product->parent_category_id = $this->_getParentCategoryId($Product->getAdvertiserCategoryId());
			$results[] = $Product;
		}
		return $results;
	}

    private function _getParentCategoryId($advertiserCategoryId)
    {
        $this->_parentIds = array();
        if(!isset($this->_parentIds[$advertiserCategoryId])){
            $table = new Application_Model_AdvertiserCategories();
            $advCategory = $table->fetch($advertiserCategoryId);
            $this->_parentIds[$advertiserCategoryId] = $advCategory->getCategoryId();

        }
        return $this->_parentIds[$advertiserCategoryId];
    }

	public function getProductsCount()
	{
		$ids = array();
		$retailersIds = $this->_getRetailersIds();
		foreach ($this->getAdvertiserCategories() as $cACategory) {
			$ids[] = $cACategory->getId();
		}

		foreach ($this->getSubcategories() as $sub) {
			foreach ($sub->getAdvertiserCategories() as $subACategory) {
				$ids[] = $subACategory->getId();
			}
		}
		if (count($ids) == 0 || count($retailersIds)==0) {
			return 0;
		}
		$table = new Application_Model_Products;
		$select = $table->select('*')
				->group('similarity')
				->where('`advertiser_category_id` IN(' . implode(',', $ids) . ')')
				->where('`retailer_id` IN(' . implode(',', $retailersIds) . ')');
		return $table->fetchAll($select)->count();
	}

	private function _getRetailersIds()
	{
		$Table = new Application_Model_Retailers;
		$result = array();
		
		if($this->getParentId() > 0){
			$where = '(category_id IS NULL OR category_id = '. $this->getParentId() .')';
		}else{
			$where = '(category_id IS NULL OR category_id = '. $this->getId() .')';
		}
		$where .= " AND state is null";
		foreach ($Table->fetchAll($where) as $retailer){
			$result[] = $retailer->getId();
		}
		
		return $result;
	}
    private function _getSpecificRetailersIds()
	{
		$Table = new Application_Model_Retailers;
		$result = array();

		if($this->getParentId() > 0){
			$where = '(category_id = '. $this->getParentId() .')';
		}else{
			$where = '(category_id = '. $this->getId() .')';
		}
		$where .= " AND state is null";
		foreach ($Table->fetchAll($where) as $retailer){
			$result[] = $retailer->getId();
		}

		return $result;
	}

    private function getParent()
    {
        $table = new Application_Model_Categories;
        return $table->fetch($this->getParentId());
    }
}