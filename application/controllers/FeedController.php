<?php
class FeedController extends Zend_Controller_Action
{
	
	public function cjHandleTestAction()
	{
		$feedProcessor = new Bigbek\Api\FeedProcessor;
		$feedProcessor->process();
	}

	public function cjHandleAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		//$formData = $this->_request->getPost();
$upload = new Zend_File_Transfer();
$files = $upload->getFileInfo();
//Zend_Debug::dump($files);

$destinationDir = APPLICATION_PATH .'/../data/';
foreach($files as $file){
	echo $file['tmp_name'] ."--->";
	echo $destinationDir.$file['name'] ."|";
	
	Zend_Debug::dump(move_uploaded_file($file['tmp_name'], $destinationDir .$file['name']));
}


$upload->receive();
return;
		/* @var $logger Zend_Log */
		$logger = Zend_Registry::get('logger');
		$logger->log(Zend_Debug::dump($formData), Zend_Log::DEBUG); 
	}
}