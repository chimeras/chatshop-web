<?php
/**
 * User: vahram
 * Date: 1/23/13
 * Time: 12:05 PM
 */
namespace Bigbek\Api\Retailers;
class Yves extends Common
{
    public function __construct()
    {
        array_push($this->_blacklistKeywords, 'men');
        array_push($this->_blacklistKeywords, 'women');
    }


    public function connectCategoryProduct($product)
    {
        $connectionsTable = new \Application_Model_CategoryXProducts;
        $connectionsTable->delete('product_id=' . $product->getId());
        $topCategoryId = $this->_retailer->getCategoryId();
        $connection = $connectionsTable->createRow();
        $connection->setFromArray(array(
            'product_id' => $product->getId(),
            'category_id' => $topCategoryId,
            'retailer_id' => $product->getRetailerId(),
            'brand_id' => $product->getBrandId(),
            'type' => 2,
            'similarity' => $product->getSimilarity()));
        $connection->save();

        foreach ($this->_processor->getProcessedCategories() as $id => $category) {
            if($category['object']->getParentCategoryId() != $topCategoryId){
                continue;
            }
            $type = 0;
            if ($category['object']->getParentId() > 0
                && $this->_checkKwd($category['object']->getKeywords(), $product->getAdvertiserKeywords())){
                $type = 4;
            } elseif ($this->_checkName($category['object']->getKeywords(), $product->getName())){
                $type = 2;
            }
            if ($type > 0) {
                $connection = $connectionsTable->createRow();
                $connection->setFromArray(array(
                    'product_id' => $product->getId(),
                    'category_id' => $id,
                    'retailer_id' => $product->getRetailerId(),
                    'brand_id' => $product->getBrandId(),
                    'type' => $type,
                    'similarity' => $product->getSimilarity()));
                $connection->save();
                return true;
            }

        }
    }
}