<?php
abstract class Application_Model_BaseCollection extends Zend_Db_Table_Abstract
{

	public $uniqueFields = array();
	
	/**
	 * 
	 * @param array $fields
	 * @return $_rowClass
	 */
	public function fetchUniqueBy($fields)
	{
		return $this->fetchRow(implode(' AND ', $this->_getWhereConditions($fields)));
	}

	/**
	 * 
	 * @param array $fields
	 * @param integer $count
	 * @param integer $offset
	 * @return array($_rowClass)
	 * 
	 */
	public function fetchBy($fields, $count=0, $offset=0)
	{
		return $this->fetchAll(implode(' AND ', $this->_getWhereConditions($fields)), null, $count, $offset);
	}

	/**
	 * 
	 * @param array $fields
	 * @return string
	 */
	private function _getWhereConditions($fields)
	{
		$condition = array();
		foreach ($fields as $key => $value) {
			if (strpos($key, "LIKE'")) {
				$condition[] = $key . $value;
			}elseif(is_numeric($value)) {
				$condition[] = $key . "=" . $value;
			}else{
				$condition[] = $key . "='" . $value ."'";
			}
		}
		return $condition;
	}
	
	public function getReferenceMap()
	{
		return $this->_referenceMap;
	}
}