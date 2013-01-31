<?php
/**
 * User: vahram
 * Date: 1/23/13
 * Time: 10:52 AM
 */
namespace Bigbek\Api\Retailers;


class Rockport extends Common
{
    public function connectCategoryProduct($product)
    {
        $connectionsTable = new \Application_Model_CategoryXProducts;
        $connectionsTable->delete('product_id=' . $product->getId());
        $globalCategoryIds = $this->_retailer->getCategoryIds();


        $connection = $connectionsTable->createRow();
        $connection->setFromArray(array(
            'product_id' => $product->getId(),
            'category_id' => $globalCategoryId,
            'retailer_id' => $product->getRetailerId(),
            'brand_id' => $product->getBrandId(),
            'type' => 3,
            'similarity' => $product->getSimilarity()));
        $connection->save();



        foreach ($this->_processor->getProcessedCategories() as $id => $category) {
            if (!in_array($category['object']->getId(), $globalCategoryIds)) {
                continue;
            }
            $type = 0;
            foreach ($this->_processor->getProcessedCategories() as $id => $topCategory) {
                if($topCategory['object']->getParentId() === 0
                    && $category['object']->getParentId() == $topCategory['object']->getId()
                    && $this->_checkKwd($topCategory['object']->getKeywords(), $product->getKeywords())
                ){

                }
            }
            if ($category['object']->getParentId() > 0

            ) {
                $type = 1;
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