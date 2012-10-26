<?php

class Application_Model_Images extends Application_Model_Db_Table_Images
{

	public $config = array();

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->config = Zend_Registry::get('config');
	}

	public function addImage($image)
	{
		// Process the New File
		// Check to see if Filename is already in Database
		$select = $this->select();
		$select->where('filename=?', $image);
		$row = $this->fetchRow($select);
		if ($row) {
			die("Filename already exists in Database.  Please try another file.");
		}


		// Move file to Storage Directory
		// Check/Create Storage Directoy (YYYYMMDD)
		// Temporarily set MEDIA_DIR
		$mediaDir = $this->config->imagesPath;
		$destinationDir = $mediaDir . date('Ymd');

		if (!is_dir($destinationDir)) {
			$storageDir = mkdir($destinationDir);
		}

		// Save Image
		$uploaded = is_uploaded_file($image);
		if (!$uploaded) {
			die("Image has not been uploaded");
		}
		$image_saved = move_uploaded_file($image, $destinationDir);

		if (!$image_saved) {
			die("Image could not be moved");
		}

		// Create Alternative Sizes
		// Save Data to Database Tables
		$dateObject = new Zend_Date();

		$row = $this->createRow();
		$row->filename = $image;
		$row->date_added = $dateObject->get(Zend_Date::TIMESTAMP);
		$row->date_modified = $dateObject->get(Zend_Date::TIMESTAMP);
		$row->save();

		// Fetch the ID of the newly created row
		$id = $this->_db->lastInsertId();

		// Retrieve IPTC Data
		// Retrieve EXIF Data
		// Return Image ID  
		return $id;
	}

}