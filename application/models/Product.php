<?php
class Application_Model_Product extends Application_Model_Db_Row_Product
{
	public $parent_category_id = 0;
	const VISIBILITY_VISIBLE = 1;
	const VISIBILITY_INVISIBLE = 0;
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
		return $this->getSimilarItems()->count()-1;
	}

    public function getTopCategoryId()
    {
        $connectionTable = new Application_Model_CategoryXProducts;
        $select = $connectionTable->select('*')
            ->group('similarity')
            ->where("product_id=?", $this->getId())
            ->where("category_id in(1,2,3,4,5,6,7,1001)"); //@todo get these parent categories from db not hardcode
        $connections = $connectionTable->fetchAll($select);
        if(count($connections) > 0){
            return $connections[0]->getCategoryId();
        }
        return null;
    }


    public function getImportantCategoryId()
    {
        $connectionTable = new Application_Model_CategoryXProducts;
        $select = $connectionTable->select('*')
            ->group('similarity')
            ->where("product_id=?", $this->getId())
            ->where("type=1");
        $connections = $connectionTable->fetchAll($select);
        if(count($connections) > 0){
            return $connections[0]->getCategoryId();
        }
        return null;
    }

    public function toArray()
    {
        $array = parent::toArray();
        $connection = $this->findParentRow("Application_Model_Retailers");
        if(is_object($connection)){
            $array['retailer_name'] = $connection->getName();
            $array['description'] .= ' '. $array['buy_url'];
            if($array['brand_name']=='null'){
                $array['brand_name'] = $connection->getName();;
            }

        }
        return $array;
    }
}