<?php

class Application_Model_Themes extends Application_Model_Db_Table_Themes
{

	protected $_dependentTables = 'Application_Model_ThemeXCategories';

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * 
	 * @param boolean $extended
	 * @return array Application_Model_Themes
	 */
	public function fetchAllArray($extended = true)
	{
		if ($extended) {
			$themes = array();
			foreach ($this->fetchAll() as $theme) {
				$themes[] = $theme->toArray();
			}
			return $themes;
		} else {
			return $this->fetchAll()->toArray();
		}
	}

}

