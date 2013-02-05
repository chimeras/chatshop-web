<?php
/**
 * User: vahram
 * Date: 1/23/13
 * Time: 12:05 PM
 */
namespace Bigbek\Api\Retailers;
class Bridge extends Common
{

    public function __construct()
    {
        array_push($this->_blacklistKeywords, 'men');
        array_push($this->_blacklistKeywords, 'women');
    }


    public function connectCategoryProduct($product)
    {
        $isSet = false;
        $connectionsTable = new \Application_Model_CategoryXProducts;
        $connectionsTable->delete('product_id=' . $product->getId());
        $topCategoryId = $this->_retailer->getCategoryId();
        $prKeywords = $product->getKeywordsTranslated();
        foreach ($this->_processor->getProcessedCategories() as $id => $category) {
            if($category->getParentId() != $topCategoryId){
                continue;
            }
            if ($category->getParentId() > 0
                && $this->_checkKwd($category->getKeywords(), $prKeywords)){
                $connection = $connectionsTable->createRow();
                $connection->setFromArray(array(
                    'product_id' => $product->getId(),
                    'category_id' => $id,
                    'retailer_id' => $product->getRetailerId(),
                    'brand_id' => $product->getBrandId(),
                    'type' => 4,
                    'similarity' => $product->getSimilarity()));
                $connection->save();
                $isSet = true;
            }
        }
        if(!$isSet){
            echo "\n#### skipping (bridge)". $prKeywords ;
        }else{
            $connection = $connectionsTable->createRow();
            $connection->setFromArray(array(
                'product_id' => $product->getId(),
                'category_id' => $topCategoryId,
                'retailer_id' => $product->getRetailerId(),
                'brand_id' => $product->getBrandId(),
                'type' => 1,
                'similarity' => $product->getSimilarity()));
            $connection->save();
        }
    }
}