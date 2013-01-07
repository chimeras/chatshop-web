<?php

class Application_Model_Category extends Application_Model_Db_Row_Category
{

    public $products = array();
    private $_subcategories = null;
    private $_parentIds = array();
    private $_parentSubCategoryIds = array();

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

    public function toCombinedArray($productsCount = 20, $offset = 0, $randomise=false)
    {
        $category = $this->toArray();
        $category['products'] = array();
        $productsCollection = $this->getProducts($productsCount, $offset, $randomise);
        foreach ($productsCollection as $Product) {
            $productArray = $Product->toArray();
            $productArray['parent_category_id'] = $Product->parent_category_id;
            $productArray['similar_items_count'] = $Product->getSimilarItemsCount();
            $category['products'][] = $productArray;
        }
        $category['subcategories'] = $this->getSubcategoriesArray();
        $category['products_qty'] = $this->getProductsCount();
        $category['this_page_products_qty'] = count($productsCollection);

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

    public function getProducts($rowCount, $page, $isRandom = false)
    {

        $table = new Application_Model_Products;
        $connectionsTable = new Application_Model_CategoryXProducts;
        $results = array();
        foreach($connectionsTable->fetchAll("category_id=".$this->getId(), 'id DESC', $rowCount, $page) as $connection){
            $product = $table->fetch($connection->getProductId());
            $product->parent_category_id = $this->_getProductSubCategoryId($product->getKeywords());
            $results[$product->getSimilarity()] = $product;
        }
        if($isRandom){
            shuffle($results);
        }

        return $results;
    }


    private function _getParentCategoryId($advertiserCategoryId)
    {
        $this->_parentIds = array();

        if (!isset($this->_parentIds[$advertiserCategoryId])) {
            $table = new Application_Model_AdvertiserCategories();
            $advCategory = $table->fetch($advertiserCategoryId);
            $this->_parentIds[$advertiserCategoryId] = $advCategory->getCategoryId();

        }
        return $this->_parentIds[$advertiserCategoryId];
    }

    private function _getProductSubCategoryId($keywords)
    {
        $this->_parentSubCategoryIds = array();
        $subcategories = $this->getSubcategories();
        foreach ($subcategories as $subcategory) {
            if (strstr($keywords, $subcategory->getName())) {
                return $subcategory->id;
            }
        }
        return $this->getId();
    }


    public function getProductsCount()
    {
        $connectionsTable = new Application_Model_CategoryXProducts;
        $select = $connectionsTable->select('*')
            ->group('similarity')
            ->where("category_id=?", $this->getId());
        $rec = $connectionsTable->fetchAll($select)->count();

        return $rec;
    }

    public function getRetailersIds()
    {
        $Table = new Application_Model_Retailers;
        $result = array();

        if ($this->getParentId() > 0) {
            $where = '(category_id IS NULL /*OR category_id = ' . $this->getParentId() . '*/)';
        } else {
            $where = '(category_id IS NULL /*OR category_id = ' . $this->getId() . '*/)';
        }
        $where .= " AND state is null";
        foreach ($Table->fetchAll($where) as $retailer) {
            $result[] = $retailer->getId();
        }
        //echo $where; exit();
        return $result;
    }

    public function getSpecificRetailersIdsInverse()
    {
        $Table = new Application_Model_Retailers;
        $result = array();
        if ($this->getParentId() > 0) {
            $where = '(category_id IS NULL OR category_id != ' . $this->getParentId() . ')';
        } else {
            $where = '(category_id IS NULL OR category_id != ' . $this->getId() . ')';
        }
        $where .= " AND state is null";
        foreach ($Table->fetchAll($where) as $retailer) {
            $result[] = $retailer->getId();
        }
        return $result;
    }

    public function getSpecificRetailersIds()
    {
        $Table = new Application_Model_Retailers;
        $result = array();

        if ($this->getParentId() > 0) {
            $where = '(category_id = ' . $this->getParentId() . ')';
        } else {
            $where = '(category_id = ' . $this->getId() . ')';
        }
        $where .= " AND state is null";
        foreach ($Table->fetchAll($where) as $retailer) {
            $result[] = $retailer->getId();
        }

        return $result;
    }

    public function getParent()
    {
        $table = new Application_Model_Categories;
        return $table->fetch($this->getParentId());
    }
}