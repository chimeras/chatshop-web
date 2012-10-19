<?php

namespace Bigbek\Api;

use \Zend_Registry;

/**
 * Commission junction wrapper class
 *
 * @author vahram
 */
class CommissionJunction
{

	private static $_cjDevKey = "008a7b4b7d1f961c58a0bd4ea80d7fadc7f6cd5bb5494dc0928e0c62a25f10ff6f8a4e9d2c3ea1dd7f65228e0cd865ba41422f83d02adc6aad5b058ac3133e4c7d/4768eaf57bcb4994be56c06fd27e2b9bf8fdceb3be3834fc9df96f0771e46f034f5e64ca9d91332a84b1fa1dc70657747ca112307354994d6e41ea18c172ee55";
	private static $_websiteId = "6359885";
	private static $_targetUrl = "https://product-search.api.cj.com/v2/";
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
		
		$targeturl = self::$_targetUrl."product-search?";
		$targeturl .= "website-id=". self::$_websiteId;
		/*$advs = "joined"; // results from (joined), (CIDs), (Empty String), (notjoined)
		$targeturl.="&amp;advertiser-ids=$advs";*/
		if(isset($query['keywords'])){
			$query['keywords'] = urlencode($query['keywords']);
		}
		$parts = array();
		foreach($query as $key => $value){
			$parts[] = $key .'='. $value;
		}
		$targeturl .= '&'. implode('&', $parts);
		echo $targeturl .'<hr />';
		$ch = curl_init($targeturl);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . self::$_cjDevKey)); // send development key
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($ch);
		/*var_dump($response);	/*	
		if(is_string($response)){
			$result = array('error'=>$response);
		}else{*/
			$result = new \SimpleXMLElement($response);
		/*}*/
		
		curl_close($ch);
		return $result;
	}

}

//http://api-product.skimlinks.com/query?q=%2Bcountry:US%20manufacturer:Samsung%20q.op=AND%20title:galaxy%20df=title%2&version=3&key=d417381991e9c817add3b6179f1f71e8&start=1000











//https://product-search.api.cj.com/v2/product-search?website-id=6359885&keywords=shoes&serviceable-area=US
//https://product-search.api.cj.com/v2/product-search?website-id=6359885&keywords=shoes&serviceable-area=CA