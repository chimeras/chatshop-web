<?php

class CronController extends Zend_Controller_Action
{

	public function init()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		set_time_limit(60);
	}

	public function bringFeedAction()
	{
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
				fwrite($fp, $content);
				fclose($fp);
			}
		}


		$feedProcessor = new Bigbek\Api\FeedProcessor;
		$feedProcessor->process();
	}

}