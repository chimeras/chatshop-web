<?php

namespace Bigbek\Webservice;

use \Zend_Registry;

/**
 * Base class for webservice models
 *
 * @author vahram
 */
class BaseWebservice
{

	protected $errorMessage = array();
	protected $currentUser = null;
	/**
	 *
	 * @var EntityManager
	 */
	public function __construct()
	{
		$registry = Zend_Registry::getInstance();
		$request = Zend_Registry::get('request');
		if($request->getParam('session') != null){
			$this->setUser($request->getParam('session'));
		}
	}

	/**
	 * 
	 * @param integer $code
	 * @return text/json
	 */
	
	public function getErrorMessage($code)
	{
		if(isset($this->errorMessage[$code])){
			return \Zend_Json::encode(array('message'=>$this->errorMessage[$code]));
		}else{
			return \Zend_Json::encode(array('message'=>'undefined code'));
		}
	}
	
	/**
	 * 
	 * @param string $session
	 * @return boolean
	 */
	protected function setUser($session)
	{
		$users = new \Application_Model_Users;
		$this->currentUser = $users->fetchBySession($session);
		return is_object($this->currentUser);
	}
}