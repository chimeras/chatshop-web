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
        $prKeywords =  $product->getKeywordsTranslated();
        foreach ($this->_processor->getProcessedCategories() as $id => $category) {
            $type = 2;
            if($category->getParentId() === 0
                && $this->_checkKwd($category->getKeywords(), $prKeywords)){ // top category
                foreach ($this->_processor->getProcessedCategories() as $subId => $subCategory) {
                    if($subCategory->getParentId() === $category->getId()
                        && $this->_checkKwd($subCategory->getKeywords(), $prKeywords)){ // category
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
                if($type==2){
                    echo "\n############################# skipping, no category ".$prKeywords ."\n";
                }else{
                    $type = 2;
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
            echo "\n\t###!!!!!!!!!!!!!!!!!!! skipping (pacific) ".$prKeywords ."\n";
        }
    }
}