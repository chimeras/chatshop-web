<?php
abstract class Application_Model_Db_Row_Brand extends Zend_Db_Table_Row_Abstract{

	public function __construct(array $config = array())
	{
		parent::__construct($config);
	}



	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}



	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}
} /*generated by setup*/