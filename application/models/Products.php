<?php

class Application_Model_Products extends Application_Model_Db_Table_Products
{

    protected $_referenceMap = array(
        'Brand' => array(
            'columns' => array('brand_id'), /* foreign key */
            'refTableClass' => 'Application_Model_Brands',
            'refColumns' => array('id') /* primary key of parent table */
        ),
        'Retailer' => array(
            'columns' => array('retailer_id'), /* foreign key */
            'refTableClass' => 'Application_Model_Retailers',
            'refColumns' => array('id') /* primary key of parent table */
        ),
        'AdvertiserCategory' => array(
            'columns' => array('advertiser_category_id'), /* foreign key */
            'refTableClass' => 'Application_Model_AdvertiserCategories',
            'refColumns' => array('id') /* primary key of parent table */
        ),
        'Category' => array(
            'columns' => array('advertiser_category_id'), /* foreign key */
            'refTableClass' => 'Application_Model_Categories',
            'refColumns' => array('id') /* primary key of parent table */
        )
    );


    public function fetchAllArray()
    {
        $objects = $this->fetchAll();
        $array = array();
        foreach ($objects as $object) {
            $array[] = $object->toArray();
        }
    }


    public function getCategorySpecificSelect(Application_Model_Category $category, $rowCount, $page)
    {
        $retailersIds = $category->getRetailersIds();
        //  var_dump($retailersIds);
        if (count($retailersIds) > 0) {
            $retailersIdsString = '`retailer_id` IN(' . implode(',', $retailersIds) . ')';
        } else {
            $retailersIdsString = 'false';
        }
        $subIds = array();
        $keywords = array();
        $mandatoryKeywords = explode(', ', $category->getKeywords());
        if (is_object($category->getParent())) {
            $mandatoryKeywords = array_merge($mandatoryKeywords, explode(', ', $category->getParent()->getKeywords()));
        }

        foreach ($category->getSubcategories() as $sub) {
            $keywords = array_merge($keywords, explode(', ', $sub->getKeywords()));
        }


        $keywordCondition = $this->select();
        foreach ($keywords as $keyword) {
            $keywordCondition->orWhere("`keywords` LIKE ?", '% ' . $keyword . '%');
            $keywordCondition->orWhere("`keywords` LIKE ?", $keyword . '%');
        }
        if (count($keywords) == 0) {
            $keywordCondition->Where("false");
        }
        $mandatoryKeywordCondition = $this->select();
        if (count($mandatoryKeywords) > 0) {
            foreach ($mandatoryKeywords as $keyword) {
                $mandatoryKeywordCondition->orWhere("`keywords` LIKE ?", '% ' . $keyword . '%');
                $mandatoryKeywordCondition->orWhere("`keywords` LIKE ?", $keyword . '%');
            }
        } else {
            $mandatoryKeywordCondition->Where("true");
        }
        $select = $this->select('*')
            ->group('similarity')
            ->where('`visible`=?', Application_Model_Product::VISIBILITY_VISIBLE)
            ->where($retailersIdsString)
            ->where(implode(' ', $mandatoryKeywordCondition->getPart(Zend_Db_Select::WHERE)))
            ->where(implode(' ', $keywordCondition->getPart(Zend_Db_Select::WHERE)))
            ->limitPage($page, $rowCount);
      //  echo $select . "\n\n\n";
        $this->_logger = \Zend_Registry::get('calls_logger');
        $this->_logger->log('get products sql for category id'. $category->getId() .'; sql='.$select, \Zend_Log::DEBUG);
        return $this->fetchAll($select);
    }
}