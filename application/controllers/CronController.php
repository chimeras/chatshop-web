<?php

class CronController extends Zend_Controller_Action
{
	
	public function init()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
	}
	
	public function bringFeedAction()
	{
		$ftpProcessor = new \Bigbek\Api\FtpProcessor;
		$files = $ftpProcessor->processDownload(false);
		var_dump($files);
		
	}
}