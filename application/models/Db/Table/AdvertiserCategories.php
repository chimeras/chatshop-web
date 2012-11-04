<?php
abstract class Application_Model_Db_Table_AdvertiserCategories extends Application_Model_BaseCollection
{
	protected $_name = 'advertiser_category';
	protected $_rowClass = 'Application_Model_AdvertiserCategory';
//	protected $_primary = 'advertiser_category_id';
	protected $_primary = 'id';
/**
	 *
	 * @param integer $id
	 * @return Application_Model_AdvertiserCategory 
	 */
	public function fetch($id)
	{
		//return $this->fetchRow('advertiser_category_id=' . $id);
		return $this->fetchRow('id=' . $id);
	}
} /*generated by setup*/