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
        'Retailer' => 'PROGRAMNAME' /*,
        'AdvertiserCategory' => 'ADVERTISERCATEGORY'*/
    );

    private $_blacklistKeywords = array('dr shoes', 'Menage');

    public function __construct()
    {
        $this->_logger = Zend_Registry::get('logger');
        $this->_productFeedTable = new \Application_Model_ProductFeeds;
        $this->_filesPath = APPLICATION_PATH . '/../data/';
        $categoriesTable = new \Application_Model_Categories;

        foreach ($categoriesTable->fetchAll() as $obj) {
            $parent = $obj->getParent();
            if (is_object($parent) && $parent->getParentId() == 0) {
                $parentAddition = ',' . $parent->getKeywords();
            } else {
                $parentAddition = '';
            }

            $this->_categories[$obj->getId()] = array('object'=>$obj, 'parentKeywords'=>$parentAddition);
        }
    }

    public function process()
    {
        $files = $this->_getFiles();
        foreach ($files as $file) {
            echo 'processing: ' . $file->getFilename() . "\n";

            $fileData = $this->_getData($file);
            $qty = $this->_writeToDb($fileData);
            $file->setProcessedAt(date("Y-m-d h:i:s"));
            $file->setRecordsProcessed($qty);
            $file->setStatus('processed');
            $file->save();
        }
    }


    /**
     *
     * @return \Application_Model_ProductFeed
     */
    private function _getFiles()
    {
        $feed = $this->_productFeedTable->fetchAll("status ='new'");
        if(count($feed) > 0){
           return $feed;
        }else{
            $this->_productFeedTable->update(array('status'=>'new'), "status !='error'");
            return $this->_productFeedTable->fetchAll("status ='new'");
        }
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

        $max = 10000;
        $count = $source = 0;
        foreach ($data as $row) {
            $source++;
            if (!isset($row['SKU'])) {
                echo "\n error with";
                var_dump($row);
                continue;
            }
            $count++;
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


            foreach ($this->_cjObjects as $obj => $cjField) {
                if (!isset($row[$cjField])) {
                    break;
                }
                $setterName = 'set' . $obj;

                $string = str_replace("\\'", '', $row[$cjField]);
                $string = str_replace('"', '', $string);

                $product->$setterName($string);
            }
            $product->setUpdatedAt(date("Y-m-d H:i:s"));
            $product->save();
            try {
                @$visible = $product->getImageUrl() != null && false !== file_get_contents($product->getImageUrl());
            } catch (\Exception $e) {
                echo "\n" . 'ERROR ### cannot get image, '.$product->getImageUrl() .', skipping product';
                $visible = false;
            }
            if ($visible) {
                $this->_connectCategoryProduct($product);
            }
            if (--$max <= 0) {
                break;
            }
        }
        echo 'processed -> from :' . $source . ', succeed:' . $count . 'products;' . "\n";
        return $count;
    }


    private function _connectCategoryProduct($product)
    {
        $retailersTable = new \Application_Model_Retailers;
        $connectionsTable = new \Application_Model_CategoryXProducts;
        $connectionsTable->delete('product_id=' . $product->getId());
        $retailer = $retailersTable->fetch($product->getRetailerId());

        foreach ($this->_categories as $id => $category) {

            $type = 0;
            if($this->_checkName($category['object']->getKeywords().$category['parentKeywords'], $product->getName())) {
                $type = 4;
            }  elseif($category['object']->getParentId()>0
                && $product->getTopCategoryId() > 0
                && $this->_checkKwd($category['object']->getKeywords().$category['parentKeywords'], $product->getAdvertiserKeywords())
                ) {
                $type = 3;
            }  elseif ($category['object']->getParentId()>0
                && $product->getTopCategoryId() > 0
                && $this->_checkKwd($category['object']->getKeywords().$category['parentKeywords'], $product->getKeywords())
                ) {
                $type = 2;
            } elseif ($retailer->getCategoryId() == $id) {
                $type = 1;
            }

            if ($type > 0) {
                $connection = $connectionsTable->fetchNew();
                $connection->setFromArray(array(
                    'product_id' => $product->getId(),
                    'category_id' => $id,
                    'type' => $type,
                    'similarity' => $product->getSimilarity()));
                $connection->save();
            }

        }
    }

    private function _checkKwd($kwd, $haystack)
    {
        $return = 0;
        $mandatories = explode(',', $kwd);
        foreach ($mandatories as $mandatory) {
            $nonMandatories = explode('|', $mandatory);
            foreach ($nonMandatories as $nonMandatory) {
                if (strstr($haystack, $nonMandatory)) {
                    foreach($this->_blacklistKeywords as $blacklistKwd){
                        if(strstr($nonMandatory, $blacklistKwd) && strstr($haystack, $blacklistKwd)){
                            continue 2;
                        }
                    }
                    $return++;
                }
            }
        }
        return $return > 0 && count($mandatories) == $return;
    }



    private function _checkName($kwd, $name)
    {
        $return = 0;
        $mandatories = explode(',', $kwd);
        foreach ($mandatories as $mandatory) {
            $nonMandatories = explode('|', $mandatory);
            foreach ($nonMandatories as $nonMandatory) {
                if (strstr($name, $nonMandatory)) {
                    foreach($this->_blacklistKeywords as $blacklistKwd){
                        if(strstr($nonMandatory, $blacklistKwd) && strstr($name, $blacklistKwd)){
                            continue 2;
                        }
                    }
                    $return++;
                }
            }
        }
        return $return > 0 && count($mandatories) == $return;
    }
}