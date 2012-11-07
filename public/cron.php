<?php
require('mode.php');
define("DONT_RUN_APP", true);
require('index.php');
$application->bootstrap();


/* bring feed */

$ftpProcessor = new \Bigbek\Api\FtpProcessor;
$files = $ftpProcessor->processDownload(false);
$targetDir = APPLICATION_PATH . '/../data/';
$filter = new Zend_Filter_Decompress(array('options' => array(
				'target' => $targetDir,
				)));
foreach ($files as $file) {
	$txtFileName = substr($file, 0, -3);
	$content = $filter->filter($targetDir . $file);
	if (is_string($content)) {
		$fp = fopen($targetDir . $txtFileName, "w");
		if(is_bool($fp)){
			echo 'something wrong with :'. $targetDir . $txtFileName ."\t";
		}else{
			fwrite($fp, $content);
			fclose($fp);
		}
	}
}


$feedProcessor = new Bigbek\Api\FeedProcessor;
$feedProcessor->process();


