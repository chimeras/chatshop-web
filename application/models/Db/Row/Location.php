<?php
abstract class Application_Model_Db_Row_Location extends Application_Model_BaseItem
{
	protected $_table = 'Application_Model_Locations';
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

	public function getLatitude()
	{
		return $this->latitude;
	}

	public function setLatitude($latitude)
	{
		$this->latitude = $latitude;
		return $this;
	}

	public function getLongitude()
	{
		return $this->longitude;
	}

	public function setLongitude($longitude)
	{
		$this->longitude = $longitude;
		return $this;
	}
} /*generated by setup*/