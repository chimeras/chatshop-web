<?php

namespace Bigbek\Api;

use \Zend_Registry;
/**
 * Skimlink wrapper class
 *
 * @author vahram
 */
class Skimlink
{
	/**
	 *
	 * @var \Zend_Rest_Client 
	 */
	protected $client;
	public function __construct()
	{
		
	}
	
	public function getProducts($query = array())
	{
		
		
		$uri = array();
		foreach ($query as $key => $val){
			$uri[] = $key .'='. urlencode($val);
		}
		$uri = implode('&', $uri);
		$url = 'http://api-product.skimlinks.com/query?'.$uri;
		$json = file_get_contents($url);
		return \Zend_Json::decode($json);
	}
}


//http://api-product.skimlinks.com/query?q=%2Bcountry:US%20manufacturer:Samsung%20q.op=AND%20title:galaxy%20df=title%2&version=3&key=d417381991e9c817add3b6179f1f71e8&start=1000