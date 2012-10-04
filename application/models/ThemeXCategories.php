<?php
class Application_Model_ThemeXCategories extends Application_Model_Db_Table_ThemeXCategorys
{
	protected $_referenceMap = array(
		'Theme' => array(
			'columns' => array('theme_id'), /*foreign key*/
			'refTableClass' => 'Application_Model_Themes',
			'refColumns' => array('id') /*primary key of parent table*/
		),
		'Category' => array(
			'columns' => array('category_id'), /*foreign key*/
			'refTableClass' => 'Application_Model_Categories',
			'refColumns' => array('id') /*primary key of parent table*/
		)
	);
	
}