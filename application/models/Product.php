<?php
class Application_Model_Product extends Application_Model_Db_Row_Product
{
	public function setSimilarity($similarity = NULL)
	{
		if($similarity == NULL){
			$similarity = $this->generateSimilarity();
		}
		parent::setSimilarity($similarity);
		return $this;
	}
	
	public function generateSimilarity()
	{
		return md5($this->getRetailerId().$this->getName());
	}
	
	public function getSimilarItems()
	{
		$Collection = new Application_Model_Products;
		return $Collection->fetchBy(array('similarity'=>$this->getSimilarity()));
	}
	
	public function getSimilarItemsCount()
	{
		return $this->getSimilarItems()->count();
	}
}