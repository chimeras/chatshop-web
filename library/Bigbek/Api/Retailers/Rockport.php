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
        $prKeywords = str_replace('>', ' ', $product->getKeywordsTranslated());
        $type = 0;
        foreach ($this->_processor->getProcessedCategories() as $categoryId => $category) {
            if (!in_array($categoryId, $globalCategoryIds)) {
                continue;
            }

            foreach ($this->_processor->getProcessedCategories() as $topCategoryId => $topCategory) {

                if($topCategory['object']->getParentId() === 0
                    && $category['object']->getParentId() === $topCategory['object']->getId()
                    && $this->_checkKwd($category['object']->getKeywords(), $prKeywords)
                    && $this->_checkKwd($topCategory['object']->getKeywords(), $prKeywords)
                ){

                    $connection = $connectionsTable->createRow();
                    $connection->setFromArray(array(
                        'product_id' => $product->getId(),
                        'category_id' => $topCategoryId,
                        'retailer_id' => $product->getRetailerId(),
                        'brand_id' => $product->getBrandId(),
                        'type' => 1,
                        'similarity' => $product->getSimilarity()));
                    $connection->save();
                    echo ', top_category_id='.$topCategoryId;

                    $type = 3;
                    $connection = $connectionsTable->createRow();
                    $connection->setFromArray(array(
                        'product_id' => $product->getId(),
                        'category_id' => $categoryId,
                        'retailer_id' => $product->getRetailerId(),
                        'brand_id' => $product->getBrandId(),
                        'type' => $type,
                        'similarity' => $product->getSimilarity()));
                    $connection->save();
                    echo ', category_id='.$categoryId;

                }
            }
        }
        if(!isset($type) || $type == 0){
            echo "\n\t###!!!!!!!!!!!!!!!!!!! skipping ".$prKeywords ."\n";
        }
    }
}