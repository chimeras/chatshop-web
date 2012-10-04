<?php

namespace Bigbek\Webservice;
/**
 * Authentication handler class
 *
 * @author vahram
 */
class ShopWebservice extends BaseWebservice
{

	private $_shoplists;
	protected $errorMessage = array(
		'2001' => 'no session or user',
		'2002' => 'Shopping List doesn\'t exist'
	);

	public function __construct()
	{
		$this->_themes = new \Application_Model_Themes;
		parent::__construct();
	}

	/**
	 * @return string JSON
	 */
	public function getThemes()
	{
		$themesArray = $this->_themes->fetchAllArray();
		
		//return \Zend_Json::encode(array('theme' => $themes[0]->toArray(), 'message' => 'successfully retreived'));
		return \Zend_Json::encode(array('themes' => $themesArray, 'message' => 'successfully retreived'));
	}

	public function getCagetoryProducts($id)
	{
		
	}
}
