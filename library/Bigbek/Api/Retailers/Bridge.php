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


            $connection = $connectionsTable->createRow();
            $connection->setFromArray(array(
                'product_id' => $product->getId(),
                'category_id' => $this->_retailer->getCategoryId(),
                'retailer_id' => $product->getRetailerId(),
                'brand_id' => $product->getBrandId(),
                'type' => 1,
                'similarity' => $product->getSimilarity()));
            $connection->save();


            $type = 0;
            if ($category['object']->getParentId() > 0
                && $this->_checkKwd($category['object']->getKeywords(), $product->getKeywords())){
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
            }

        }
    }
}