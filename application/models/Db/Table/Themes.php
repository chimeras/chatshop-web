<?php
abstract class Application_Model_Db_Table_Themes extends Application_Model_BaseCollection
{
	protected $_name = 'theme';
	protected $_rowClass = 'Application_Model_Theme';
//	protected $_primary = 'theme_id';
	protected $_primary = 'id';
/**
	 *
	 * @param integer $id
	 * @return Application_Model_Theme 
	 */
	public function fetch($id)
	{
		//return $this->fetchRow('theme_id=' . $id);
		return $this->fetchRow('id=' . $id);
	}
} /*generated by setup*/