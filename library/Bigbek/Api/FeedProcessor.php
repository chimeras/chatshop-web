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
        'image_url' => 'IMAGEURL',
        'saleprice' => 'SALEPRICE',
        'shipping_cost' => 'STANDARDSHIPPINGCOST',
        'in_stock' => 'INSTOCK',
        'online' => 'ONLINE',
        'brand_name' => 'MANUFACTURER'
    );

    private $_cjObjects = array(
        'Brand' => 'MANUFACTURER',
        'Retailer' => 'PROGRAMNAME' /*,
        'AdvertiserCategory' => 'ADVERTISERCATEGORY'*/
    );


    private $_updatedRetailers = array();
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
        echo count($files) .' files to process' ."\n";
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
     * @return \Application_Model_ProductFeeds
     */
    private function _getFiles()
    {
        $feed = $this->_productFeedTable->fetchAll("status ='new'", "records_processed");
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
        $retailersTable = new \Application_Model_Retailers;

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
                $product->$dbField = str_replace('""', '"', stripslashes($row[$cjField]));
            }
            $product->setBrandName($row[$this->_cjObjects['Brand']]);
            $product->setSimilarity();


            foreach ($this->_cjObjects as $obj => $cjField) {
                if (!isset($row[$cjField])) {
                    break;
                }
                $setterName = 'set' . $obj;

                //$string = str_replace("\\'", '', $row[$cjField]);
                //$string = str_replace('"', '', $string);
                $string = stripslashes($row[$cjField]);

                $product->$setterName($string);
            }
            $product->setUpdatedAt(date("Y-m-d H:i:s"));
            $product->save();
            echo "\n\t\t ". $product->getId() ."\t". $product->getName();

            try {
                @$visible = $product->getImageUrl() != null && false !== file_get_contents($product->getImageUrl());
            } catch (\Exception $e) {
                echo "\n" . 'ERROR ### cannot get image, '.$product->getImageUrl() .', skipping product';
                $visible = false;
            }
            if ($visible) {
                $retailer = $retailersTable->fetch($product->getRetailerId());
                if($retailer->getState()=='disabled'){
                    continue;
                }
                if (!array_key_exists($retailer->getId(), $this->_updatedRetailers)) {
                    $date = $retailer->setLastUpdate(date("Y-m-d H:i:s"));
                    $retailer->save();
                    $this->_updatedRetailers[$retailer->getId()] = $date;
                }
                $connector = $retailer->getProcessorObject();
                $connector->setProcessor($this);
                $connector->connectCategoryProduct($product);
                $product->setVisible(1);
                $product->save();
            }
            if (--$max <= 0) {
                break;
            }
        }
        echo 'processed -> from :' . $source . ', succeed:' . $count . 'products;' . "\n";
        return $count;
    }


    public function cleanup()
    {
        echo "\n\n cleaning up \n";
        $i = $j = 0;
        $table = new \Application_Model_Products;

        foreach($table->fetchAll("visible = 1") as $product){
            $i++;
            $hide = false;
            if(array_key_exists($product->getRetailerId(), $this->_updatedRetailers)){
                $retailerDate = new \DateTime($this->_updatedRetailers[$product->getRetailerId()]->getLastUpdate());
                $productDate = new \DateTime($product->getUpdatedAt());
                $interval = date_diff($retailerDate, $productDate);
                if($interval->format('H') > 3){
                    echo $interval->format('H') .' hours passed before last update, hiding product';
                    $hide = true;
                }

            }
            if($hide == false){
                try {
                   @$hide = ($product->getImageUrl() == null) || (bool)(false != file_get_contents($product->getImageUrl()));
                } catch (\Exception $e) {
                    echo "\n" . 'ERROR ### cannot get image, '.$product->getImageUrl() .', hiding product';
                    $hide = true;
                }
            }


            if($hide){
                $product->setVisible(0);
                foreach($product->findDependentRowset("Application_Model_CategoryXProducts") as $connection){
                    $connection->delete();
                }
                $j++;
                $product->save();
            }
            //echo "\n ok:". $product->getId();
        }
        echo "\t processed ". $i .' items, removed'.$j ."items \n###################################################\n";
    }


    public function getProcessedCategories()
    {
        return $this->_categories;
    }
}