<?php
/**
 * User: vahram
 * Date: 1/23/13
 * Time: 10:52 AM
 */
namespace Bigbek\Api\Retailers;


class Thebay
{
    protected $_retailer, $_processor;
    protected $_blacklistKeywords = array('dc shoes', 'menage');
    public function setRetailer($retailer)
    {
        $this->_retailer = $retailer;
    }
    public function setProcessor($processor)
    {
        $this->_processor = $processor;
    }


    public function connectCategoryProduct($product)
    {
        $connectionsTable = new \Application_Model_CategoryXProducts;
        $connectionsTable->delete('product_id=' . $product->getId());
        $prAdvCategory =  $product->getAdvertiserCategoryTranslated();
        foreach ($this->_processor->getProcessedCategories() as $id => $category) {
            $type = 1;
            if($category->getParentId() === 0
                && $this->_checkKwd($category->getKeywords(), $prAdvCategory)){ // top category


                foreach ($this->_processor->getProcessedCategories() as $subId => $subCategory) {
                    if($subCategory->getParentId() === $category->getId()
                    && $this->_checkKwd($subCategory->getKeywords(), $prAdvCategory)){ // category
                        $type = 4;
                        echo ', category_id='.$subId;
                        // set category
                        $connection = $connectionsTable->createRow();
                        $connection->setFromArray(array(
                            'product_id' => $product->getId(),
                            'category_id' => $subId,
                            'retailer_id' => $product->getRetailerId(),
                            'brand_id' => $product->getBrandId(),
                            'type' => $type,
                            'similarity' => $product->getSimilarity()));
                        $connection->save();
                    }
                }
                if($type==1){
                    echo "\n############################# skipping, no category ".$prAdvCategory ."\n";
                }else{
                    $type = 1;
                    echo ', top_category_id='.$id;
                    // set top category
                    $connection = $connectionsTable->createRow();
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
        if(!isset($type)){
            echo "\n\t###!!!!!!!!!!!!!!!!!!! skipping (common) ".$prAdvCategory ."\n";
        }
    }

    protected function _checkKwd($kwd, $haystack)
    {
        $return = 0;
        $haystack = strtolower($haystack);
        $mandatories = explode(',', strtolower($kwd));
        foreach ($mandatories as $mandatory) {
            if ($mandatory == '') {
                continue;
            }
            $nonMandatories = explode('|', $mandatory);
            foreach ($nonMandatories as $nonMandatory) {

                if (strstr($haystack, ' ' . $nonMandatory) || strstr($haystack, '|' . $nonMandatory) || strpos($haystack, $nonMandatory) === 0) {
                    foreach ($this->_blacklistKeywords as $blacklistKwd) {
                        if(strstr($nonMandatory, 'shoes') && strstr($haystack, 'dc shoes')){
                            echo "\n #1##compare### blc:".$blacklistKwd." \t\t to nonmandatory:".$nonMandatory ."\t\t in haystack:".$haystack."\n";
                        }

                        if (strstr($blacklistKwd, $nonMandatory) && strstr($haystack, $blacklistKwd)) {
                            echo 'skipping' . $nonMandatory . ' because of ' . $haystack . "\n";
                            continue 2;
                        }
                    }
                    $return++;
                }
            }
        }
        return $return > 0 && count($mandatories) == $return;
    }


    protected function _checkName($kwd, $name)
    {
        $return = 0;
        $name = strtolower($name);
        $mandatories = explode(',', strtolower($kwd));
        foreach ($mandatories as $mandatory) {
            if ($mandatory == '') {
                continue;
            }
            $nonMandatories = explode('|', $mandatory);
            foreach ($nonMandatories as $nonMandatory) {
                if (strstr($name, ' ' . $nonMandatory) || strpos($name, $nonMandatory) === 0) {
                    foreach ($this->_blacklistKeywords as $blacklistKwd) {
                        if(strstr($nonMandatory, 'shoes') && strstr($name, 'dc shoes')){
                            echo "\n #2##compare### blc:".$blacklistKwd." \t\t to nonmandatory:".$nonMandatory ."\t\t in haystack:".$name."\n";
                        }
                        if (strstr($blacklistKwd, $nonMandatory) && strstr($name, $blacklistKwd)) {
                            echo 'skipping(2)' . $nonMandatory . ' because of ' . $name . "\n";
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