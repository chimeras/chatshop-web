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
	}

	/**
	 * @return string JSON
	 */
	public function getThemes()
	{
		//$themesArray = $this->_themes->fetchAllArray();
		
		//return \Zend_Json::encode(array('theme' => $themes[0]->toArray(), 'message' => 'successfully retreived'));
		$themes = $this->_themes->fetchAll();
		$return = array();
		foreach ($themes as $theme){
			$themeArray = $theme->toArray();
			$themeArray['categories'] = $theme->getCategoriesArray();
			$return[] = $themeArray;
			
		}
		
		
		
		
		return \Zend_Json::encode(array('themes' => $return, 'message' => 'successfully retreived'));
	}
/*
	public function getCategoryProducts($id)
	{
		$category = $this->_categories->fetch($id);
		return \Zend_Json::encode(array('products' => $category->getProductsArray(), 'message' => 'successfully retreived'));
	}*/
	
	public function getCategories()
	{
		/*$categoriesTable = new \Bigbek\Api\CommissionJunction;
		return \Zend_Json::encode(array('products' => $categoriesTable->getCategories(), 'message' => 'successfully retreived'));*/
		
		$categoriesTable = new \Application_Model_AdvertiserCategories;
		return \Zend_Json::encode(array('categories' => $categoriesTable->fetchAllArray(), 'message' => 'successfully retreived'));
	}
	
	/**
	 * 
	 * @param integer $id
	 * @param integer $page
	 * @param integer $limit
	 * @return JSON
	 */
	public function getCategoryProducts($id, $page, $limit = 50)
	{
		$productTable = new \Application_Model_Products;
		$products = $productTable->fetchBy(array('advertiser_category_id'=>$id), $limit, $page*$limit);
		$arrProducts = array();
		foreach($products as $product){
			$arrProducts[] = $product->toArray();
		}
		return \Zend_Json::encode(array('products' => $arrProducts, 'message' => 'successfully retreived'));
	}
}
