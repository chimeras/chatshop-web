<?php
abstract class Application_Model_Db_Row_Indexer extends Application_Model_BaseItem
{
	protected $_table = 'Application_Model_Indexers';
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



	public function getKeywords()
	{
		return $this->keywords;
	}

	public function setKeywords($keywords)
	{
		$this->keywords = $keywords;
		return $this;
	}

	public function getAdvertiserKeywords()
	{
		return $this->advertiser_keywords;
	}

	public function setAdvertiserKeywords($advertiser_keywords)
	{
		$this->advertiser_keywords = $advertiser_keywords;
		return $this;
	}

	public function getAdvertiserType()
	{
		return $this->advertiser_type;
	}

	public function setAdvertiserType($advertiser_type)
	{
		$this->advertiser_type = $advertiser_type;
		return $this;
	}
} /*generated by setup*/