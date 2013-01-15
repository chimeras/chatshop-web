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

    private $_blacklistKeywords = array('dc shoes', 'menage');
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
                $product->$dbField = addslashes(str_replace('""', '"', trim($row[$cjField], '"')));
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
            echo "\n\t\t ". $product->getName();

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
        if(!array_key_exists($retailer->getId(), $this->_updatedRetailers)){
            $date = $retailer->setLastUpdate(date("Y-m-d H:i:s"));
            $retailer->save();
            $this->_updatedRetailers[$retailer->getId()] = $date;
        }
        foreach ($this->_categories as $id => $category) {

            $type = 0;

          /*  if((strstr(strtolower($product->getName()), 'shoe') || strstr(strtolower($product->getKeywords()), 'shoe'))
                && strstr(strtolower($category['object']->getKeywords().$category['parentKeywords']), 'shoe')){
                echo "\n#####################################\n product_id=".$product->getId();
                echo "\n required-kwds=".$category['object']->getKeywords()
                    .", parent=".$category['parentKeywords']."\n\t name=". $product->getName()
                    ."\n\t adv-kwd=".$product->getAdvertiserKeywords()
                    ."\n\t kwd=".$product->getKeywords()."\n\t";
                echo "chk-name()=". (int)$this->_checkName($category['object']->getKeywords().$category['parentKeywords'], $product->getName()) ."\n\t";
                echo "chk-adv-kwd()=". (int)$this->_checkKwd($category['object']->getKeywords().$category['parentKeywords'], $product->getAdvertiserKeywords()) ."\n\t";
                echo "chk-kwd(".$category['object']->getKeywords().$category['parentKeywords'].")=". (int)$this->_checkKwd($category['object']->getKeywords().$category['parentKeywords'], $product->getKeywords());

            }*/


            if($this->_checkName($category['object']->getKeywords().$category['parentKeywords'], $product->getName())) {
                $type = 5;
            }elseif($category['object']->getParentId()==0 && (
                $this->_checkKwd($category['object']->getKeywords(), $product->getAdvertiserKeywords()
                || $this->_checkKwd($category['object']->getKeywords(), $product->getKeywords()))
            )){
                $type = 1;

            }  elseif($category['object']->getParentId()>0
              /*  && $product->getTopCategoryId() > 0*/
                && $this->_checkKwd($category['object']->getKeywords().$category['parentKeywords'], $product->getAdvertiserKeywords())
                ) {
                $type = 4;
            }  elseif ($category['object']->getParentId()>0
              /*  && $product->getTopCategoryId() > 0*/
                && $this->_checkKwd($category['object']->getKeywords().$category['parentKeywords'], $product->getKeywords())
                ) {
                $type = 3;
            } elseif ($retailer->getCategoryId() == $id) {
                $type = 2;
            }

            if ($type > 0) {
                $connection = $connectionsTable->fetchNew();
                $connection->setFromArray(array(
                    'product_id' => $product->getId(),
                    'category_id' => $id,
                    'retailer_id' => $product->getRetailerId(),
                    'brand_id' => $product->getBrandId(),
                    'type' => $type,
                    'similarity' => $product->getSimilarity()));
                $connection->save();
            }

        }
    }

    private function _checkKwd($kwd, $haystack)
    {
        $return = 0;
        $haystack = strtolower($haystack);
        $mandatories = explode(',', strtolower($kwd));
        foreach ($mandatories as $mandatory) {
            $nonMandatories = explode('|', $mandatory);
            foreach ($nonMandatories as $nonMandatory) {
                if (strstr($haystack, ' '. $nonMandatory) || strpos($haystack, $nonMandatory) == 0) {
                    foreach($this->_blacklistKeywords as $blacklistKwd){
                        if(strstr($blacklistKwd, $nonMandatory) && strstr($haystack, $blacklistKwd)){
                            echo 'skipping'.  $nonMandatory .' because of '. $haystack ."\n";
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
        $name = strtolower($name);
        $mandatories = explode(',', strtolower($kwd));
        foreach ($mandatories as $mandatory) {
            $nonMandatories = explode('|', $mandatory);
            foreach ($nonMandatories as $nonMandatory) {
                if (strstr($name, ' '. $nonMandatory) || strpos($name, $nonMandatory)==0) {
                    foreach($this->_blacklistKeywords as $blacklistKwd){
                        if(strstr($blacklistKwd, $nonMandatory) && strstr($name, $blacklistKwd)){
                            echo 'skipping(2)'.  $nonMandatory .' because of '. $name ."\n";
                            continue 2;
                        }
                    }
                    $return++;
                }
            }
        }
        return $return > 0 && count($mandatories) == $return;
    }



    public function cleanup()
    {
        echo "\n\n cleaning up";
        $i = $j = 0;
        $table = new \Application_Model_Products;
        foreach($table->fetchAll("visible = 1") as $product){
            $i++;
            $hide = false;
            if(array_key_exists($product->getRetailerId(), $this->_updatedRetailers)){
                $retailerDate = new DateTime($this->_updatedRetailers[$product->getRetailerId()]);
                $productDate = new DateTime($product->getUpdatedAt());
                $interval = date_diff($retailerDate, $productDate);
                echo $interval->format('H') .' hours passed before last update';
                $hide = true;
            }
            try {

                @$visible = $product->getImageUrl() != null && false !== file_get_contents($product->getImageUrl());
            } catch (\Exception $e) {
                echo "\n" . 'ERROR ### cannot get image, '.$product->getImageUrl() .', hiding product';
                $hide = true;
            }

            if($hide){
                $product->setVisible(0);
                foreach($product->findDependentRowset("Application_Model_CategoryXProducts") as $connection){
                    $connection->delete();
                }
                $j++;
                $product->save();
            }
        }
        echo "\t processed ". $i .' items, removed'.$j ."items \n###################################################\n";
    }
}