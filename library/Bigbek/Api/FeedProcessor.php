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

    /**
     * @var array
     */
    private $_categories;

    private $_cjFields = array(
        'name' => 'NAME',
        'description' => 'DESCRIPTION',
        'keywords' => 'KEYWORDS',
        'advertiser_keywords' => 'ADVERTISERCATEGORY',
        'sku' => 'SKU',
        'upc' => 'UPC',
       // 'isbn' => 'ISBN',
        'currency' => 'CURRENCY',
        'price' => 'PRICE',
        'buy_url' => 'BUYURL',
        'impression_url' => 'IMPRESSIONURL',
        'image_url' => 'IMAGEURL'
    );

    private $_cjObjects = array(
        'Brand' => 'MANUFACTURER',
        'Retailer' => 'PROGRAMNAME'/*,
        'AdvertiserCategory' => 'ADVERTISERCATEGORY'*/
    );

    private $_blacklistKeywords = array('dr shoes');
    public function __construct()
    {
        $this->_logger = Zend_Registry::get('logger');
        $this->_productFeedTable = new \Application_Model_ProductFeeds;
        $this->_filesPath = APPLICATION_PATH . '/../data/';
        $categoriesTable = new \Application_Model_Categories;
        foreach($categoriesTable->fetchAll() as $obj){
            $this->_categories[$obj->getId()] = $obj->getKeywords();
        }
    }

    public function process()
    {
        $files = $this->_getFiles();
        foreach ($files as $file) {
            echo 'processing: ' . $file->getFilename() . "\n";

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


        for ($i = 1; $i < count($lines); $i++) {
            $lineData = explode("\t", $lines[$i]);
            $data = array();
            foreach ($headers as $key => $header) {
                if (!isset($lineData[$key])) {
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
     //   $indexerTable = new \Application_Model_Indexers;

        $max = 10000;
        $count = $source = 0;
        foreach ($data as $row) {
            $source++;
            if (!isset($row['SKU'])) {
                var_dump($row);
                continue;
            }
            $count++;
            /*	foreach(array_keys($row) as $key){
                    echo $key .'<br />';
                }
                break;*/
            $product = $productTable->fetchUniqueBy(array('sku' => $row['SKU']));
            if (!is_object($product)) {
                $product = $productTable->fetchNew();
            } elseif (strtotime($product->getUpdatedAt()) + 3600 > time()) {
                continue;
            }


            foreach ($this->_cjFields as $dbField => $cjField) {
                if (!isset($row[$cjField])) {
                    break;
                }
                $product->$dbField = str_replace("'", "", $row[$cjField]);
            }
            $product->setBrandName($row[$this->_cjObjects['Brand']]);
            $product->setSimilarity();

            $product->save();
            foreach ($this->_cjObjects as $obj => $cjField) {
                if (!isset($row[$cjField])) {
                    break;
                }
                $setterName = 'set' . $obj;
                $product->$setterName(addslashes($row[$cjField]));
            }
            $product->setUpdatedAt(date("Y-m-d H:i:s"));
         //   $product->setKeywords($product->getKeywords() .' ,, '. $row['ADVERTISERCATEGORY']);
            $product->save();
            $this->_connectCategoryProduct($product);

       /*     if ($product->getVisible() == 1) {
                $indexer = $indexerTable->fetch($product->getId());
                if (!is_object($indexer)) {
                    $indexer = $indexerTable->fetchNew();
                }

                $indexer->setId($product->getId());
                $indexer->setRetailerId($product->getRetailerId());
                $indexer->setSimilarity($product->getSimilarity());
                $indexer->setKeywords($product->getKeywords());
                $indexer->setAdvertiserKeywords($row['ADVERTISERCATEGORY']);
                $indexer->save();
            }*/
            if (--$max <= 0) {
                break;
            }
        }
        echo 'processed -> from :' . $source . ', successed:' . $count . 'products;' . "\n";
        return TRUE;
    }


    private function _connectCategoryProduct($product)
    {
        $retailersTable = new \Application_Model_Retailers;
        $connectionsTable = new \Application_Model_CategoryXProducts;
        $connectionsTable->delete('product_id='. $product->getId());
        $retailer = $retailersTable->fetch($product->getRetailerId());
        foreach($this->_categories as $id => $category){
            foreach(explode(',', $category) as $kwd){
                if(strstr($product->getKeywords(), $kwd) && !in_array($kwd, $this->_blacklistKeywords)){

                    $connection = $connectionsTable->fetchNew();
                    $connection->setFromArray(array('product_id'=>$product->getId(), 'category_id'=>$id));
                    $connection->save();
                }elseif(strstr($product->getAdvertiserKeywords(), $kwd)){
                    $connection = $connectionsTable->fetchNew();
                    $connection->setFromArray(array('product_id'=>$product->getId(), 'category_id'=>$id));
                    $connection->save();
                }elseif($retailer->getCategoryId() == $id){
                    $connection = $connectionsTable->fetchNew();
                    $connection->setFromArray(array('product_id'=>$product->getId(), 'category_id'=>$id));
                    $connection->save();
                }

            }

        }
    }
}