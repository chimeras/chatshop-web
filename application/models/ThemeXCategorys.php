<?php
class Application_Model_ThemeXCategories extends Application_Model_Db_Table_ThemeXCategorys
{
	protected $name = 'theme_x_category';
	protected $_referenceMap = array(
		'Theme' => array(
			'columns' => array('theme_id'), /*foreign key*/
			'refTableClass' => 'Application_Model_Themes',
			'refColumns' => array('id') /*primary key of parent table*/
		),
		'Category' => array(
			'columns' => array('cateogry_id'), /*foreign key*/
			'refTableClass' => 'Application_Model_Categories',
			'refColumns' => array('id') /*primary key of parent table*/
		)
	);
	
}