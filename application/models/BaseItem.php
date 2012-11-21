<?php
abstract class Application_Model_BaseItem extends Zend_Db_Table_Row_Abstract
{

	public function __call($method, array $args)
	{
		$setters = $this->_getReferencedSetters();
		if (array_key_exists($method, $setters)) {
			$object = $this->_setGenerated($setters[$method], $args);
			foreach($this->getFieldNames($setters[$method]) as $column){
				$methodName = 'set'.$this->toCamelCase($column);
				$this->$methodName($object->getId());
			}
		}
	}

	protected function getFieldNames($relatedTableName)
	{
		$table = new $this->_table;
		if ($table->getReferenceMap() == array()) {
			return array();
		}
		
		foreach ($table->getReferenceMap() as $instanceBody) {
			if($relatedTableName == $instanceBody['refTableClass']){
				return $instanceBody['columns'];
			}
		}
		
	}
	
	private function _getReferencedSetters()
	{
		$table = new $this->_table;
		
		if ($table->getReferenceMap() == array()) {
			return array();
		}
		$setInstances = array();
		foreach ($table->getReferenceMap() as $instanceName => $instanceBody) {
			$setInstances['set' . $instanceName] = $instanceBody['refTableClass'];
		}
		return $setInstances;
	}
	

	private function _setGenerated($refTableClass, $arguments)
	{
		/* @var $table Zend_Db_Table_Abstract */
		$table = new $refTableClass;
		if(count($table->uniqueFields) == 0){
			throw new Exception($refTableClass .' must have at least one unique field');
		}
		$unique = array_combine($table->uniqueFields, $arguments);
		$object = $table->fetchUniqueBy($unique);
		if(!is_object($object)){
			$object = $table->fetchNew();
			$object->setFromArray($unique);
			$object->save();
		}
		
		return $object;
	}

	
	public function toCamelCase($string, $capitalizeFirstCharacter = true)
	{

		$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

		if (!$capitalizeFirstCharacter) {
			$str[0] = strtolower($str[0]);
		}

		return $str;
	}
}