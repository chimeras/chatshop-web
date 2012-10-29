<?php
abstract class Application_Model_Db_Row_Product extends Application_Model_BaseItem
{
	protected $_table = 'Application_Model_Products';
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

	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}

	public function getColor()
	{
		return $this->color;
	}

	public function setColor($color)
	{
		$this->color = $color;
		return $this;
	}

	public function getSize()
	{
		return $this->size;
	}

	public function setSize($size)
	{
		$this->size = $size;
		return $this;
	}

	public function getPrice()
	{
		return $this->price;
	}

	public function setPrice($price)
	{
		$this->price = $price;
		return $this;
	}

	public function getBrandId()
	{
		return $this->brand_id;
	}

	public function setBrandId($brand_id)
	{
		$this->brand_id = $brand_id;
		return $this;
	}

	public function getRetailerId()
	{
		return $this->retailer_id;
	}

	public function setRetailerId($retailer_id)
	{
		$this->retailer_id = $retailer_id;
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

	public function getSku()
	{
		return $this->sku;
	}

	public function setSku($sku)
	{
		$this->sku = $sku;
		return $this;
	}

	public function getUpc()
	{
		return $this->upc;
	}

	public function setUpc($upc)
	{
		$this->upc = $upc;
		return $this;
	}

	public function getIsbn()
	{
		return $this->isbn;
	}

	public function setIsbn($isbn)
	{
		$this->isbn = $isbn;
		return $this;
	}

	public function getCurrency()
	{
		return $this->currency;
	}

	public function setCurrency($currency)
	{
		$this->currency = $currency;
		return $this;
	}

	public function getSaleprice()
	{
		return $this->saleprice;
	}

	public function setSaleprice($saleprice)
	{
		$this->saleprice = $saleprice;
		return $this;
	}

	public function getBuyUrl()
	{
		return $this->buy_url;
	}

	public function setBuyUrl($buy_url)
	{
		$this->buy_url = $buy_url;
		return $this;
	}

	public function getImpressionUrl()
	{
		return $this->impression_url;
	}

	public function setImpressionUrl($impression_url)
	{
		$this->impression_url = $impression_url;
		return $this;
	}

	public function getImageUrl()
	{
		return $this->image_url;
	}

	public function setImageUrl($image_url)
	{
		$this->image_url = $image_url;
		return $this;
	}

	public function getAdvertiserCategoryId()
	{
		return $this->advertiser_category_id;
	}

	public function setAdvertiserCategoryId($advertiser_category_id)
	{
		$this->advertiser_category_id = $advertiser_category_id;
		return $this;
	}

	public function getInStock()
	{
		return $this->in_stock;
	}

	public function setInStock($in_stock)
	{
		$this->in_stock = $in_stock;
		return $this;
	}
} /*generated by setup*/