<?php
class Application_Model_Db_Row_CategoryProduct extends Zend_Db_Table_Row_Abstract{

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

	public function getId()
	{
		return $this->id;
	}

	public function getCategoryId()
	{
		return $this->category_id;
	}

	public function setCategoryId($category_id)
	{
		$this->category_id = $category_id;
		return $this;
	}

	public function getProductId()
	{
		return $this->product_id;
	}

	public function setProductId($product_id)
	{
		$this->product_id = $product_id;
		return $this;
	}
} /*generated by setup*/