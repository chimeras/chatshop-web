<?php
abstract class Application_Model_Db_Table_Indexers extends Application_Model_BaseCollection
{
	protected $_name = 'indexer';
	protected $_rowClass = 'Application_Model_Indexer';
//	protected $_primary = 'indexer_id';
	protected $_primary = 'id';
/**
	 *
	 * @param integer $id
	 * @return Application_Model_Indexer 
	 */
	public function fetch($id)
	{
		//return $this->fetchRow('indexer_id=' . $id);
		return $this->fetchRow('id=' . $id);
	}
} /*generated by setup*/