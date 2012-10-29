<?php
abstract class Application_Model_Db_Row_Image extends Application_Model_BaseItem
{
	protected $_table = 'Application_Model_Images';
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



	public function getFileName()
	{
		return $this->file_name;
	}

	public function setFileName($file_name)
	{
		$this->file_name = $file_name;
		return $this;
	}

	public function getState()
	{
		return $this->state;
	}

	public function setState($state)
	{
		$this->state = $state;
		return $this;
	}

	public function getCreatedAt()
	{
		return $this->created_at;
	}

	public function setCreatedAt($created_at)
	{
		$this->created_at = $created_at;
		return $this;
	}
} /*generated by setup*/