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
        $retailersIds = $category->getSpecificRetailersIdsInverse();
        $specificRetailersIds = $category->getSpecificRetailersIds();
        if (count($retailersIds) > 0) {
            $retailersIdsString = '`retailer_id` IN(' . implode(',', $retailersIds) . ')';
        } else {
            $retailersIdsString = 'false';
        }

        if (count($specificRetailersIds) > 0) {
            $specificRetailersIdsString = '`retailer_id` IN(' . implode(',', $specificRetailersIds) . ')';
        } else {
            $specificRetailersIdsString = 'false';
        }
        $keywords = array();
        /*
         * var $mandatoryKeywords
         * keywords of this particular category, include also parent category
         */

        /*
         * var $specificMandatoryKeywords
         * keywords only from this particular category
         *
         */
        $mandatoryKeywords = $specificMandatoryKeywords = array($category->getKeywords());
        if (is_object($category->getParent())) {
            $mandatoryKeywords[] = $category->getParent()->getKeywords();
        }
        foreach ($category->getSubcategories() as $sub) {
            $keywords[] = $sub->getKeywords();
        }


        $keywordCondition = $this->select();
        if (count($keywords) > 0) {
            foreach ($keywords as $keyword) {
                $keywordCondition->orWhere($this->_getSqlVersion($keyword));
            }
        } else {
            $keywordCondition->Where("true");
        }


        $mandatoryKeywordCondition = $this->select();
        if (count($mandatoryKeywords) > 0) {
            foreach ($mandatoryKeywords as $keyword) {
                $mandatoryKeywordCondition->Where($this->_getSqlVersion($keyword));
            }
        }

        $specificMandatoryKeywordCondition = $this->select();
        if (count($specificMandatoryKeywords) > 0) {
            foreach ($specificMandatoryKeywords as $keyword) {
                $specificMandatoryKeywordCondition->Where($this->_getSqlVersion($keyword));
            }
        }
        /* Select products having current category name, and subcategories name from retailers not categorised by this category*/
        $selectUsual = $this->select('*')
            ->group('similarity')
            ->where('`visible`=?', Application_Model_Product::VISIBILITY_VISIBLE)
            ->where($retailersIdsString)
            ->where(implode(' ', $mandatoryKeywordCondition->getPart(Zend_Db_Select::WHERE)))
            ->where(implode(' ', $keywordCondition->getPart(Zend_Db_Select::WHERE)));

        /* Select products having current category name, and subcategories name from retailers whose category is current one*/
        $selectSpecific = $this->select('*')
            ->group('similarity')
            ->where('`visible`=?', Application_Model_Product::VISIBILITY_VISIBLE)
            ->where($specificRetailersIdsString)
            ->where(implode(' ', $specificMandatoryKeywordCondition->getPart(Zend_Db_Select::WHERE)))
            ->where(implode(' ', $keywordCondition->getPart(Zend_Db_Select::WHERE)));
            $select = $this->select()->union(array($selectUsual, $selectSpecific))->limitPage($page, $rowCount)->order('RAND()');
    //    echo "\n=========================================\n".$select."\n";
        $this->_logger = \Zend_Registry::get('calls_logger');
        $this->_logger->log('get products sql for category id' . $category->getId() . '; sql=' . $select, \Zend_Log::DEBUG);
        return $this->fetchAll($select);
    }


    /**
     * @param string $keywordsString
     * @param string $columnName
     * @return string
     */
    private function _getSqlVersion($keywordsString, $columnName = 'keywords')
    {
        $sqlString = '';
        $sqlParts = array();
        $parts = explode(',', $keywordsString);
        foreach($parts as $part){
            $orPartsProcessed = array();
            $orParts = explode('|', $part);
            foreach($orParts as $orPart){
                $orPart = trim($orPart);
                $orPartsProcessed[] = $columnName." LIKE '$orPart%'";
                $orPartsProcessed[] = $columnName." LIKE '% $orPart%'";
                $orPartsProcessed[] = $columnName." LIKE '%,$orPart%'";
                $orPartsProcessed[] = $columnName." LIKE '%/$orPart%'";
            }
            $sqlParts[] = '('. implode(' OR ', $orPartsProcessed) .')';
        }
        $sqlString = implode(' AND ', $sqlParts);
        return $sqlString;
    }
}