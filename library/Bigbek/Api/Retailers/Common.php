<?php
/**
 * User: vahram
 * Date: 1/23/13
 * Time: 10:52 AM
 */
namespace Bigbek\Api\Retailers;

use \Zend_Registry;

class Common
{
    protected $_retailer, $_processor;
    protected $_blacklistKeywords = array('dc shoes', 'menage');
    public function __construct($retailer)
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

        foreach ($this->_processor->getProcessedCategories() as $id => $category) {

            $type = 0;
            $topCategoryId = $product->getTopCategoryId();
            if ($this->_checkName($category['object']->getKeywords() . $category['parentKeywords'], $product->getName())) {
                $type = 5;
            } elseif ($category['object']->getParentId() > 0
                && $topCategoryId > 0
                && $this->_checkKwd($category['object']->getKeywords() . $category['parentKeywords'], $product->getAdvertiserKeywords())
            ) {
                $type = 4;
            } elseif ($category['object']->getParentId() > 0
                && $topCategoryId > 0
                && $this->_checkKwd($category['object']->getKeywords() . $category['parentKeywords'], $product->getKeywords())
            ) {
                $type = 3;
            } elseif ($this->_retailer->getCategoryId() == $id) {
                $type = 2;
            } elseif ($category['object']->getParentId() == 0 && (
                $this->_checkKwd($category['object']->getKeywords(), $product->getAdvertiserKeywords())
                    || $this->_checkKwd($category['object']->getKeywords(), $product->getKeywords())
                    || $this->_checkName($category['object']->getKeywords(), $product->getName())
            )
            ) {
                if ($topCategoryId > 0) {
                    $connectionsTable->delete("product_id=" . $product->getId() . " AND category_id=" . $topCategoryId);
                }
                $type = 1;
            }

            if ($type > 0) {
                $connection = $connectionsTable->fetchNew();
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
                if (strstr($haystack, ' ' . $nonMandatory) || strpos($haystack, $nonMandatory) === 0) {
                    foreach ($this->_blacklistKeywords as $blacklistKwd) {
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