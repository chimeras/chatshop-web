<?php

class Application_Model_Uploads
{

	public $config = array();

	public function __construct()
	{
		$this->config = Zend_Registry::get('config');
	}

	public function addImage($id, $imageData)
	{
		$mediaDir = $this->config->imagesPathPrivate;
		$destinationDir = $mediaDir . floor($id / 100);

		if (!is_dir($destinationDir)) {
			mkdir($destinationDir);
		}
		$fileName = $destinationDir . '/reminder_' . $id . '.png';
		$logger = \Zend_Registry::get('logger');
		echo $fileName;
		$logger->log('addImage: save image as ' . $fileName, \Zend_Log::DEBUG);
		$gzFileData = base64_decode($imageData);
		$logger->log('gzcompressed: ' . substr($gzFileData, 0, 50) ."\n". substr($gzFileData, -50), \Zend_Log::ERR);
		$data = gzuncompress();
		$im = $data;
		try {
			$file = fopen($fileName, "w");
			fwrite($file, $im);
			fclose($file);
			return $fileName;
		} catch (Exception $e) {
			$logger->log('addImage: ' . $e->getMessage(), \Zend_Log::ERR);
			return $e->getMessage();
		}
	}

}