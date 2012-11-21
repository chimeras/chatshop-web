<?php

class Application_Model_Uploads
{

	public $config = array();

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->config = Zend_Registry::get('config');
	}

	public function addImage($id, $imageData)
	{
		$mediaDir = $this->config->imagesPathPrivate;
		$destinationDir = $mediaDir . floor($id / 100);
		
		if (!is_dir($destinationDir)) {
			mkdir($destinationDir);
		}
		$fileName = $destinationDir . '/reminder_'. $id .'.png';
		$logger = \Zend_Registry::get('logger');
		
		$logger->log('addImage: save image as '. $fileName, \Zend_Log::DEBUG);
		
		$data = base64_decode($imageData);
		$im = imagecreatefromstring($data);
		
		$file = fopen($fileName, "w");
		fwrite($file, $data);
		return $fileName;
	}

}