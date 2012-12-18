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
        $cacheID = 'theme_categories_'.$id;
        $categories = $cache->load($cacheID);
        if ($categories === false) {
            $theme = $this->_themes->fetch($id);
            $categories = $theme->getCategoriesArray();
            $cache->save($categories);
        }else{
            shuffle($categories);
        }
        return \Zend_Json::encode(array('categories' => $categories, 'message' => 'successfully retrieved'));
    }

    /**
     *
     * @param integer $id
     * @param integer $page
     * @param integer $limit
     * @return JSON
     */
    public
    function getCategoryProducts($id, $page = 1, $limit = 50)
    {
        $cache = \Zend_Registry::get('cache');
        $cacheID = 'category_products_'.$id.'_'.$page.'_'.$limit;
        $this->_logger->log('getCategoryProducts for id=' . $id . ',$page = ' . $page . ', $limit = ' . $limit, \Zend_Log::INFO);
        $page = $page > 0 ? (int)$page : 1;
        $limit = (int)$limit;

        $categoriesTable = new \Application_Model_Categories;
        $Category = $categoriesTable->fetch($id);
        if (!is_object($Category)) {
            return \Zend_Json::encode(array('error' => 2005, 'message' => 'no such category'));
        }

        $arrCategory = $cache->load($cacheID);
        if($arrCategory === false){
            $arrCategory = $Category->toCombinedArray($limit, $page);
            $cache->save($arrCategory);
        }else{
            shuffle($arrCategory);
        }


        return \Zend_Json::encode(
            array('products' => $arrCategory['products'],
                'products_qty' => $arrCategory['products_qty'],
                'message' => 'successfully retreived')
        );
    }


    /**
     * @todo finish this service
     *
     */
    public
    function getRecommendations($keywords)
    {
        $apAdapter = new \Bigbek\Api\CommissionJunction;
        $productsArray = $apAdapter->getProducts(array('keywords' => $keywords));
        $cjProcessor = new \Bigbek\Api\CjProcessor;
        $products = $cjProcessor->generateFromArray($productsArray);
        var_dump($productsArray);
    }

}