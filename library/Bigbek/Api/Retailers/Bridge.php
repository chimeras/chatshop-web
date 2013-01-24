<?php
/**
 * User: vahram
 * Date: 1/23/13
 * Time: 12:05 PM
 */
namespace Bigbek\Api\Retailers;
class Bridge extends Common
{


    public function connectCategoryProduct($product)
    {
        $connectionsTable = new \Application_Model_CategoryXProducts;
        $connectionsTable->delete('product_id=' . $product->getId());

        foreach ($this->_processor->getProcessedCategories() as $id => $category) {

            $type = 0;
            $topCategoryId = $product->getTopCategoryId();
            if ($this->_retailer->getCategoryId() == $id) {
                $type = 1;
            }elseif ($this->_checkName($category['object']->getKeywords() . $category['parentKeywords'], $product->getName())) {
                $type = 2;
            }elseif ($category['object']->getParentId() > 0
                && $topCategoryId > 0
                && $this->_checkKwd($category['object']->getKeywords() . $category['parentKeywords'], $product->getKeywords())
            ) {
                $type = 3;
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
            }

        }
    }
}