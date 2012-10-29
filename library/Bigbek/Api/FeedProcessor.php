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
	 * @var string
	 */
	private $_filesPath;
	/**
	 *
	 * @var \Application_Model_ProductFeeds
	 */
	private $_productFeedTable;

	
	private $_cjFields = array(
		'name'			=> 'NAME',
		'description'	=> 'DESCRIPTION',
		'keywords'		=> 'KEYWORDS',
		'sku'			=> 'SKU',
		'upc'			=> 'UPC',
		'isbn'			=> 'ISBN',
		'currency'		=> 'CURRENCY',
		'price'			=> 'PRICE',
		'buy_url'		=> 'BUYURL',
		'impression_url'=> 'IMPRESSIONURL',
		'image_url'		=> 'IMAGEURL'
	);
	
	private $_cjObjects = array(
		'Brand'				=> 'MANUFACTURER',
		'Retailer'			=> 'PROGRAMNAME',
		'AdvertiserCategory'=> 'ADVERTISERCATEGORY'
	);
	
	public function __construct()
	{
		$this->_logger = Zend_Registry::get('logger');
		$this->_productFeedTable = new \Application_Model_ProductFeeds;
		$this->_filesPath = APPLICATION_PATH .'/../data/';
	}

	public function process()
	{
		$files = $this->_getFiles();
		foreach ($files as $file) {
			echo ' '.$file->getFilename();
			
			$fileData = $this->_getData($file);
			$this->_writeToDb($fileData);
		}
	}

	
	/**
	 * 
	 * @return \Application_Model_ProductFeed
	 */
	private function _getFiles()
	{
		return $this->_productFeedTable->fetchAll();
	}

	/**
	 * 
	 * @param \Application_Model_ProductFeed $file
	 * @return array
	 */
	private function _getData(\Application_Model_ProductFeed $file)
	{
		$structure = array();
		$content = file_get_contents($this->_filesPath . $file->getFilename());
		$lines = explode("\n", $content);
		$headers = explode("\t", $lines[0]);
		
		
		for($i=1; $i<count($lines); $i++){
			$lineData = explode("\t", $lines[$i]);
			$data = array();
			foreach ($headers as $key => $header){
				if(!isset($lineData[$key])){
					continue;
				}
				$data[$header] = $lineData[$key];
			}
			$structure[] = $data;
		}
		return $structure;
	}

	/**
	 * 
	 * @param array $data
	 * @return boolean
	 */
	private function _writeToDb($data)
	{
		$productTable = new \Application_Model_Products;
		$max = 1000;
		foreach ($data as $row){
		/*	foreach(array_keys($row) as $key){
				echo $key .'<br />';
			}
			break;*/
			$product = $productTable->fetchNew();
			
			foreach($this->_cjFields as $dbField => $cjField){
				if(!isset($row[$cjField])){
					break;
				}
				$product->$dbField = $row[$cjField];
			}
			$product->save();
			foreach($this->_cjObjects as $obj => $cjField){
				if(!isset($row[$cjField])){
					break;
				}
				$setterName = 'set'.$obj;
				$product->$setterName($row[$cjField]);
				echo ":\n";
			}
			
			$product->save();
			if(--$max<=0){
				break;
				
			}
		}
		return TRUE;
	}
}