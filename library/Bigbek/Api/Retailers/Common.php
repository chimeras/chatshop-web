<?php
/**
 * User: vahram
 * Date: 1/23/13
 * Time: 10:52 AM
 */
namespace Bigbek\Api\Retailers;


class Common
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
        $isSet = false;
        $prAdvCategory = $product->getAdvertiserCategoryTranslated();
        $prKeywords = $product->getKeywordsTranslated();
        $systemCategories = $this->_processor->getProcessedCategories();
        $topCategory = $this->_checkForTopCategory($prAdvCategory); // check in advertiser category first
        if($topCategory === false){
            $topCategory = $this->_checkForTopCategory($prKeywords); // check in product keywords
            if($topCategory === false && $this->_retailer->getCategoryId() > 0){
                $topCategory = $systemCategories[$this->_retailer->getCategoryId()];
            }
        }
        if(is_object($topCategory)){
            $categories = $this->_checkForChildCategories($prAdvCategory, $topCategory);
            if(count($categories) == 0){
                $categories = $this->_checkForChildCategories($prKeywords, $topCategory);
                if(count($categories) == 0){
                    echo "\n\t###!!!!!!!!!!!!!!!!!!! skipping (no category)" . $prAdvCategory . "\n";
                }
            }
        }else{
            echo "\n\t###!!!!!!!!!!!!!!!!!!! skipping (no top category) " . $prAdvCategory . "\n";
        }

        if(isset($categories) && count($categories) > 0){
            $this->_setConnection($product, $topCategory, 1);
            echo "> + top_category_id: " . $topCategory->getId();
            foreach($categories as $category){
                $this->_setConnection($product, $category, 3);
                echo " > +++ category_id: " . $category->getId();
                $subcategories = $this->_checkForChildCategories($prAdvCategory, $category);
                if(count($subcategories) == 0){
                    $subcategories = $this->_checkForChildCategories($prKeywords, $category);
                    if(count($subcategories) == 0){
                        echo "\t # no subcategory: " . $prAdvCategory ."\t";
                    }
                }

                if(isset($subcategories) && count($subcategories) > 0){
                    foreach($subcategories as $subcategory){
                        echo " > +++++ subcategory_id: " . $subcategory->getId();
                        $this->_setConnection($product, $subcategory, 5);
                    }
                }
            }
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
                        if (strstr($nonMandatory, 'shoes') && strstr($haystack, 'dc shoes')) {
                            echo "\n #1##compare### blc:" . $blacklistKwd . " \t\t to nonmandatory:" . $nonMandatory . "\t\t in haystack:" . $haystack . "\n";
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
                        if (strstr($nonMandatory, 'shoes') && strstr($name, 'dc shoes')) {
                            echo "\n #2##compare### blc:" . $blacklistKwd . " \t\t to nonmandatory:" . $nonMandatory . "\t\t in haystack:" . $name . "\n";
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




    protected function _checkForTopCategory($kwd)
    {
        foreach ($this->_processor->getProcessedCategories() as $category) {
            if ($category->getParentId() === 0
                && $this->_checkKwd($category->getKeywords(), $kwd)){
                    return $category;
             }
        }
        return false;
    }

    protected function _checkForChildCategories($kwd, $parent)
    {
        $categories = array();
        foreach ($this->_processor->getProcessedCategories() as $category) {
            if ($category->getParentId() === $parent->getId()
                && $this->_checkKwd($category->getKeywords(), $kwd)){
                $categories[] = $category;
            }
        }
        return $categories;
    }

    /**
     * @param \Application_Model_Product $product
     * @param \Application_Model_Category $category
     * @param int $type
     * @return mixed
     */
    protected function _setConnection(\Application_Model_Product $product, \Application_Model_Category $category, $type = 1)
    {
        $connectionsTable = new \Application_Model_CategoryXProducts;
        $connection = $connectionsTable->createRow();
        $connection->setFromArray(array(
            'product_id' => $product->getId(),
            'category_id' => $category->getId(),
            'retailer_id' => $product->getRetailerId(),
            'brand_id' => $product->getBrandId(),
            'type' => $type,
            'similarity' => $product->getSimilarity()));
        return $connection->save();
    }
}