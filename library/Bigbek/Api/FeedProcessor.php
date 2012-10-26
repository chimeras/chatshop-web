<?php

namespace Bigbek\Api;
use \Zend_Registry;

/**
 *
 * @author vahram
 */
class FeedProcessor
{
	/**
	 * @var \Zend_Log 
	 */
	private $_logger;
	/**
	 *
	 * @var Applica
	 */
	private $_productFeedTable;
	public function __construct()
	{
		$this->_logger = Zend_Registry::get('logger');
	}

	public function process()
	{
		$files = 
	}
	
}