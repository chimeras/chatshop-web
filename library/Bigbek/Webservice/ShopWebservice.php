<?php

namespace Bigbek\Webservice;

/**
 * Authentication handler class
 *
 * @author vahram
 */
class ShopWebservice extends BaseWebservice
{

    /**
     *
     * @var \Application_Model_Themes
     */
    private $_themes;

    /**
     *
     * @var \Zend_Log
     */
    private $_logger;

    /**
     *
     * @var \Application_Model_Categories
     */
    private $_categories;
    protected $errorMessage = array(
        '2001' => 'no session or user',
        '2002' => 'Shopping List doesn\'t exist'
    );

    public function __construct()
    {
        $this->_themes = new \Application_Model_Themes;
        $this->_categories = new \Application_Model_Categories;
        parent::__construct();

        $this->_logger = \Zend_Registry::get('calls_logger');

    }

    /**
     * @return string JSON
     */
    public function getThemes()
    {

        $themes = $this->_themes->fetchAll();
        $return = array();
        foreach ($themes as $theme) {
            $themeArray = $theme->toArray();
            $return[] = $themeArray;
        }
        return \Zend_Json::encode(array('themes' => $return, 'message' => 'successfully retreived'));
    }


    /**
     *
     * @param integer $id
     * @return JSON array
     */
    public function getThemeCategories($id)
    {
        $this->_logger->log('getThemeCategories for id=' . $id, \Zend_Log::INFO);
        $cache = \Zend_Registry::get('cache');
        $cacheID = 'theme_categories_' . $id . '_rand_' . rand(100, 101);
        $categories = false; //$cache->load($cacheID);
        if ($categories === false) {
            // echo 'generating ';
            $theme = $this->_themes->fetch($id);
            $categories = $theme->getCategoriesArray();
            //   $cache->save($categories); /////////////////////////////////////@@todo uncomment this to use cache
        } else {
            shuffle($categories);
        }
        return \Zend_Json::encode(array('categories' => $categories, 'message' => 'successfully retrieved'));
    }

    /**
     *
     * @return JSON
     *
     */
    public function getCategories()
    {
        $theme = $this->_themes->fetch(1);
        $categories = $theme->getAllCategories();
        return \Zend_Json::encode(array('categories' => $categories, 'message' => 'successfully retrieved'));
    }

    public function getCategoryName($id)
    {
        $categoryTable= new \Application_Model_Categories;
        $category=$categoryTable->fetch($id);
        return \Zend_Json::encode(array('name' => $category->getName(), 'message' => 'successfully retrieved'));
    }

    /**
     *
     * @param integer $id
     * @param integer $page
     * @param integer $limit
     * @return JSON
     */
    public function getCategoryProducts($id, $page = 1, $limit = 50, $retailerId = null, $brandId=null)
    {
        $cache = \Zend_Registry::get('cache');
        $cacheID = 'category_products_' . $id . '_' . $page . '_' . $limit;
        $this->_logger->log('getCategoryProducts for id=' . $id . ',$page = ' . $page . ', $limit = ' . $limit, \Zend_Log::INFO);
        $offset = $page > 0 ? ($page - 1) * $limit : 0;
        $limit = (int)$limit;

        $categoriesTable = new \Application_Model_Categories;
        $Category = $categoriesTable->fetch($id);
        if (!is_object($Category)) {
            return \Zend_Json::encode(array('error' => 2005, 'message' => 'no such category'));
        }

        $arrCategory = false; //$cache->load($cacheID);
        if ($arrCategory === false) {
            $arrCategory = $Category->toCombinedArray($limit, $offset, false, $retailerId, $brandId);
            //   $cache->save($arrCategory); /////////////////////////////////////@@todo uncomment this to use cache
        } else {
            shuffle($arrCategory['products']);
        }


        return \Zend_Json::encode(
            array('products' => $arrCategory['products'],
                'products_qty' => $arrCategory['products_qty'],
                'this_page_products_qty' => $arrCategory['this_page_products_qty'],
                'message' => 'successfully retreived')
        );
    }

    /**
     * @param array $productIds
     * @return string
     */
    public function getProductsInfo($productIds)
    {
        try {
            $productIds = \Zend_Json::decode($productIds);
        } catch (\Exception $e) {
            return \Zend_Json::encode(array('error' => '3000', 'message' => $e->getMessage()));
        }
        $table = new \Application_Model_Products;
        $productInfo = array();
        foreach($productIds as $productId){
            $product = $table->fetch($productId);
            if(is_object($product)){
                $productInfo[$productId] = $product->toArray();
            }
        }
        return \Zend_Json::encode(
            array('products' => $productInfo,
                'message' => 'successfully retreived')
        );
    }

    public function getRetailers()
    {
        $return = array();
        $table = new \Application_Model_Retailers;
        foreach($table->fetchAll() as $retailer){
            $return[] = $retailer->toArray();
        }
        return \Zend_Json::encode(
            array('retailers' => $return,
                'message' => 'successfully retreived')
        );

    }

    public function getBrands()
    {
        $return = array();
        $table = new \Application_Model_Brands;
        foreach($table->fetchAll() as $brand){
            $return[$brand->getName()] = $brand->toArray();
        }
        return \Zend_Json::encode(
            array('brands' => array_values($return),
                'message' => 'successfully retreived')
        );

    }

    /**
     * @todo finish this service
     *
     */
   /* public function getRecommendations($keywords)
    {
        $apAdapter = new \Bigbek\Api\CommissionJunction;
        $productsArray = $apAdapter->getProducts(array('keywords' => $keywords));
        $cjProcessor = new \Bigbek\Api\CjProcessor;
        $products = $cjProcessor->generateFromArray($productsArray);
        var_dump($productsArray);
    }*/
}