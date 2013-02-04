<?php
/**
 * User: vahram
 * Date: 1/23/13
 * Time: 12:05 PM
 */
namespace Bigbek\Api\Retailers;
class Pacific extends Common
{



    public function connectCategoryProduct($product)
    {
        $connectionsTable = new \Application_Model_CategoryXProducts;
        $connectionsTable->delete('product_id=' . $product->getId());
        $isSet = false;
        $prKeywords = str_replace('>', ' ', $product->getKeywordsTranslated());
        $prAdvCategory = str_replace('>', ' ', $product->getAdvertiserCategoryTranslated());
        foreach ($this->_processor->getProcessedCategories() as $id => $category) {

            $type = 0;
            $topCategoryId = $product->getTopCategoryId();
            if ($category['object']->getParentId() > 0
                && $topCategoryId > 0
                && $this->_checkKwd($category['object']->getKeywords() . $category['parentKeywords'], $prAdvCategory)
            ) {
                $type = 4;
            } elseif ($category['object']->getParentId() > 0
                && $topCategoryId > 0
                && $this->_checkKwd($category['object']->getKeywords() . $category['parentKeywords'], $prKeywords)
            ) {
                $type = 3;
            } elseif ($this->_retailer->getCategoryId() == $id) {
                $type = 2;
            } elseif ($category['object']->getParentId() == 0 && (
                $this->_checkKwd($category['object']->getKeywords(), $prAdvCategory)
                    || $this->_checkKwd($category['object']->getKeywords(), $prKeywords)
            )
            ) {
                if ($topCategoryId > 0) {
                    $connectionsTable->delete("product_id=" . $product->getId() . " AND category_id=" . $topCategoryId);
                }
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
                if($type > 2){
                    $isSet = true;
                    echo ', top_category_id='.$id;
                }else{
                    echo ', top_category_id='.$id;
                }

            }

        }
        if(!$isSet){
            echo "\n#### skipping ". $prKeywords .', NOR '. $prAdvCategory;
        }
    }
}